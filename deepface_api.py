from flask import Flask, request, jsonify
from flask_cors import CORS
from deepface import DeepFace
import cv2
import numpy as np
import base64
import tempfile
import os

app = Flask(__name__)
CORS(app)

# ===== PENINGKATAN AKURASI: THRESHOLD DIPERKETAT =====
# Nilai diturunkan dari 0.55 menjadi 0.40 untuk seleksi yang lebih ketat
DISTANCE_THRESHOLD = 0.40

def save_temp_image(img: np.ndarray) -> str:
    fd, path = tempfile.mkstemp(suffix=".jpg")
    cv2.imwrite(path, img)
    os.close(fd)
    return path

@app.route('/verify', methods=['POST'])
def verify():
    temp_img_path = None
    try:
        data = request.json
        if not data or 'image' not in data:
            return jsonify({"status": "error", "message": "Image not found in request"}), 400

        # Decode base64 image
        img_data = base64.b64decode(data['image'])
        nparr = np.frombuffer(img_data, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

        if img is None:
            raise ValueError("Gagal decode gambar.")

        img = cv2.resize(img, (300, 300))
        temp_img_path = save_temp_image(img)

        # Draw rectangle manually
        faces_detected = DeepFace.extract_faces(
            img_path=temp_img_path,
            detector_backend="opencv",
            enforce_detection=False,
            align=False
        )

        if faces_detected:
            for face in faces_detected:
                fa = face.get('facial_area', {})
                x, y, w, h = fa.get('x', 0), fa.get('y', 0), fa.get('w', 0), fa.get('h', 0)
                if w > 0 and h > 0:
                    cv2.rectangle(img, (x, y), (x + w, y + h), (0, 255, 0), 2)
        else:
            print("No face detected for framing the image.")

        # Encode image with box
        _, buffer = cv2.imencode('.jpg', img)
        img_with_frame = base64.b64encode(buffer).decode('utf-8') # type: ignore

        # Face recognition
        result = DeepFace.find(
            img_path=temp_img_path,
            db_path="faces/",
            detector_backend="opencv",
            model_name="VGG-Face", # Menggunakan model yang lebih akurat
            enforce_detection=False
        )

        best_identity_nim = None
        best_identity_name = None
        best_score = 1.0

        for df in result:
            if not df.empty:
                top = df.iloc[0]
                if top['distance'] < best_score:
                    best_score = top['distance']
                    identity_filename = os.path.basename(top['identity'])
                    
                    best_identity_nim = identity_filename.split('_')[0]
                    
                    parts = identity_filename.split('_')
                    if len(parts) > 1:
                        name_with_ext = "_".join(parts[1:])
                        name_underscored = os.path.splitext(name_with_ext)[0]
                        best_identity_name = name_underscored.replace('_', ' ')
                    else:
                        best_identity_name = "Nama tidak diketahui"
        
        # Menggunakan threshold yang sudah diperketat
        if best_identity_nim and best_score <= DISTANCE_THRESHOLD:
            return jsonify({
                "status": "success",
                "identity": best_identity_nim,
                "name": best_identity_name, 
                "score": float(best_score),
                "framed_image": img_with_frame
            })
        else:
            message = "Wajah tidak terdaftar atau tidak cocok."
            if best_identity_nim:
                message = f"Wajah terdeteksi, tapi tingkat kemiripan rendah (Skor: {best_score:.2f})."
            
            return jsonify({
                "status": "not_found",
                "framed_image": img_with_frame,
                "message": message 
            })

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500
    
    finally:
        if temp_img_path and os.path.exists(temp_img_path):
            os.remove(temp_img_path)

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000, debug=True)