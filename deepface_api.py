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

def save_temp_image(img: np.ndarray) -> str:
    fd, path = tempfile.mkstemp(suffix=".jpg")
    cv2.imwrite(path, img)
    os.close(fd)
    return path

@app.route('/verify', methods=['POST'])
def verify():
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

        # Resize image (optional but helps consistency)
        img = cv2.resize(img, (300, 300))
        temp_img_path = save_temp_image(img)

        # Draw rectangle manually
        faces = DeepFace.extract_faces(
            img_path=temp_img_path,
            detector_backend="opencv",
            enforce_detection=False,
            align=False
        )

        for face in faces:
            fa = face.get('facial_area', {})
            x, y, w, h = fa.get('x', 0), fa.get('y', 0), fa.get('w', 0), fa.get('h', 0)
            cv2.rectangle(img, (x, y), (x + w, y + h), (0, 255, 0), 2)

        # Encode image with box
        _, buffer = cv2.imencode('.jpg', img)
        img_with_frame = base64.b64encode(buffer).decode('utf-8')

        # Face recognition
        result = DeepFace.find(
            img_path=temp_img_path,
            db_path="faces/",
            detector_backend="opencv",
            model_name="Facenet",
            enforce_detection=False
        )

        best_identity = None
        best_score = 1.0

        for df in result:
            if not df.empty:
                top = df.iloc[0]
                if top['distance'] < best_score:
                    best_score = top['distance']
                    identity_filename = os.path.basename(top['identity'])  # ex: 20230001_Budi.jpg
                    best_identity = identity_filename.split('_')[0]       # ambil NIM: 20230001

        if best_identity:
            return jsonify({
                "status": "success",
                "identity": best_identity,
                "score": float(best_score),
                "framed_image": img_with_frame
            })
        else:
            return jsonify({
                "status": "not_found",
                "framed_image": img_with_frame
            })

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000)
