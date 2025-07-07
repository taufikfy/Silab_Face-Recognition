from flask import Flask, request, jsonify
from flask_cors import CORS
from deepface import DeepFace
import cv2
import numpy as np
import base64

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

        if len(result) > 0:
            identity = result.iloc[0]['identity']
            return jsonify({"status": "success", "identity": identity, "framed_image": img_with_frame})
        else:
            return jsonify({"status": "not_found", "framed_image": img_with_frame})

    except Exception as e:
        print("Error occurred:", str(e))
        return jsonify({"status": "error", "message": str(e)}), 500

   
    
    

