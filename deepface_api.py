from flask import Flask, request, jsonify
from flask_cors import CORS
from deepface import DeepFace
import cv2
import numpy as np
import base64
import tempfile
import os

def save_temp_image(img: np.ndarray) -> str:
    """
    Save an OpenCV image to a temporary .jpg file and return its absolute path.
    The file is NOT deleted automatically; you can add cleanup later if needed.
    """
    fd, path = tempfile.mkstemp(suffix=".jpg")
    cv2.imwrite(path, img)
    os.close(fd)          # close the low-level file descriptor
    return path

app = Flask(__name__)
CORS(app)

@app.route('/verify', methods=['POST'])
def verify():
    try:
        print("[INFO] Receive request")
        data = request.json
        print("[INFO] Decode image")
        img_data = base64.b64decode(data['image'])
        nparr = np.frombuffer(img_data, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
        if img is None:
            raise ValueError("Image decode failed")
        img = cv2.resize(img, (300, 300))
        print("[INFO] Image resized")

        temp_img_path = save_temp_image(img)
        print(f"[INFO] Temp image saved: {temp_img_path}")

        detections = DeepFace.extract_faces(
            img_path=temp_img_path,
            detector_backend="opencv",
            enforce_detection=False,
            align=False
        )
        print(f"[INFO] Detections: {detections}")

        for det in detections:
            facial_area = det['facial_area']
            x = facial_area.get('x', 0)
            y = facial_area.get('y', 0)
            w = facial_area.get('w', 0)
            h = facial_area.get('h', 0)
            cv2.rectangle(img, (x, y), (x + w, y + h), (0, 255, 0), 2)

        _, buffer = cv2.imencode('.jpg', img)
        img_with_frame = base64.b64encode(buffer).decode('utf-8')
        print("[INFO] Image encoded")

        result = DeepFace.find(
            img_path=temp_img_path,
            db_path="faces/",
            detector_backend="opencv",
            model_name="Facenet",
            enforce_detection=False
        )
        print(f"[INFO] Find result: {result}")

        best_identity = None
        best_score = 1.0        # distance is 0..2; smaller is better

        for df in result:
            if not df.empty:
                top = df.iloc[0]
                if top['distance'] < best_score:
                    best_score = top['distance']
                    best_identity = top['identity']

        # ---- return ----
        if best_identity is not None:
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
        #     return jsonify({"status": "success", "identity": identity, "framed_image": img_with_frame})
        # else:
        #     return jsonify({"status": "not_found", "framed_image": img_with_frame})

    except Exception as e:
        print("Error occurred:", str(e))
        return jsonify({"status": "error", "message": str(e)}), 500

   
    
    

