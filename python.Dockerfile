# python.Dockerfile
FROM python:3.10-slim

WORKDIR /app

# Tambahkan baris ini untuk meng-install dependensi sistem OpenCV
RUN apt-get update && apt-get install -y libgl1-mesa-glx

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000

CMD ["python", "deepface_api.py"]