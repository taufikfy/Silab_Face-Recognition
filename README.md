Silab Face Recognition
https://raw.githubusercontent.com/serengil/deepface/master/icon/deepface-icon-labeled.png" width="200" height="240">

DeepFace is a lightweight face recognition and facial attribute analysis (age, gender, emotion and race) framework for python. It is a hybrid face recognition framework wrapping state-of-the-art models: VGG-Face, FaceNet, OpenFace, DeepFace, DeepID, ArcFace, Dlib, SFace and GhostFaceNet.

Experiments show that human beings have 97.53% accuracy on facial recognition tasks whereas those models already reached and passed that accuracy level.

Installation
The easiest way to install deepface is to download it from PyPI. It's going to install the library itself and its prerequisites as well.

Shell

$ pip install deepface
Alternatively, you can also install deepface from its source code. Source code may have new features not published in pip release yet.

Shell

$ git clone https://github.com/serengil/deepface.git
$ cd deepface
$ pip install -e .
Once you installed the library, then you will be able to import it and use its functionalities.

Python

from deepface import DeepFace
A Modern Facial Recognition Pipeline - Demo

A modern  consists of 5 common stages: detect, align, normalize, represent and verify. While DeepFace handles all these common stages in the background, you don’t need to acquire in-depth knowledge about all the processes behind it. You can just call its verification, find or analysis function with a single line of code.

Face Verification - Demo

This function verifies face pairs as same person or different persons. It expects exact image paths as inputs. Passing numpy or base64 encoded images is also welcome. Then, it is going to return a dictionary and you should check just its verified key.

Python

result = DeepFace.verify(
  img1_path = "img1.jpg",
  img2_path = "img2.jpg",
)
https://raw.githubusercontent.com/serengil/deepface/master/icon/stock-1.jpg" width="95%" height="95%">

Face recognition - Demo

Face recognition requires applying face verification many times. Herein, deepface has an out-of-the-box find function to handle this action. It's going to look for the identity of input image in the database path and it will return list of pandas data frame as output. Meanwhile, facial embeddings of the facial database are stored in a pickle file to be searched faster in next time. Result is going to be the size of faces appearing in the source image. Besides, target images in the database can have many faces as well.

Python

dfs = DeepFace.find(
  img_path = "img1.jpg",
  db_path = "C:/workspace/my_db",
)
https://raw.githubusercontent.com/serengil/deepface/master/icon/stock-6-v2.jpg" width="95%" height="95%">

Embeddings - Demo

Face recognition models basically represent facial images as multi-dimensional vectors. Sometimes, you need those embedding vectors directly. DeepFace comes with a dedicated representation function. Represent function returns a list of embeddings. Result is going to be the size of faces appearing in the image path.

Python

embedding_objs = DeepFace.represent(
  img_path = "img.jpg"
)
This function returns an array as embedding. The size of the embedding array would be different based on the model name. For instance, VGG-Face is the default model and it represents facial images as 4096 dimensional vectors.

Python

for embedding_obj in embedding_objs:
  embedding = embedding_obj["embedding"]
  assert isinstance(embedding, list)
  assert (
    model_name == "VGG-Face"
    and len(embedding) == 4096
  )
Here, embedding is also plotted with 4096 slots horizontally. Each slot is corresponding to a dimension value in the embedding vector and dimension value is explained in the colorbar on the right. Similar to 2D barcodes, vertical dimension stores no information in the illustration.

https://raw.githubusercontent.com/serengil/deepface/master/icon/embedding.jpg" width="95%" height="95%">

Face recognition models - Demo

DeepFace is a hybrid face recognition package. It currently wraps many state-of-the-art face recognition models: VGG-Face , FaceNet, OpenFace, DeepFace, DeepID, ArcFace, Dlib, SFace and GhostFaceNet. The default configuration uses VGG-Face model.

Python

models = [
  "VGG-Face", 
  "Facenet", 
  "Facenet512", 
  "OpenFace", 
  "DeepFace", 
  "DeepID", 
  "ArcFace", 
  "Dlib", 
  "SFace",
  "GhostFaceNet",
]

#face verification
result = DeepFace.verify(
  img1_path = "img1.jpg",
  img2_path = "img2.jpg",
  model_name = models[0],
)

#face recognition
dfs = DeepFace.find(
  img_path = "img1.jpg",
  db_path = "C:/workspace/my_db", 
  model_name = models[1],
)

#embeddings
embedding_objs = DeepFace.represent(
  img_path = "img.jpg",
  model_name = models[2],
)
https://raw.githubusercontent.com/serengil/deepface/master/icon/model-portfolio-20240316.jpg" width="95%" height="95%">

FaceNet, VGG-Face, ArcFace and Dlib are overperforming ones based on experiments - see BENCHMARKS for more details. You can find the measured scores of various models in DeepFace and the reported scores from their original studies in the following table.

Model	Measured Score	Declared Score
Facenet512	98.4%	99.6%
Human-beings	97.5%	97.5%
Facenet	97.4%	99.2%
Dlib	96.8%	99.3 %
VGG-Face	96.7%	98.9%
ArcFace	96.7%	99.5%
GhostFaceNet	93.3%	99.7%
SFace	93.0%	99.5%
OpenFace	78.7%	92.9%
DeepFace	69.0%	97.3%
DeepID	66.5%	97.4%

Export to Sheets
Conducting experiments with those models within DeepFace may reveal disparities compared to the original studies, owing to the adoption of distinct detection or normalization techniques. Furthermore, some models have been released solely with their backbones, lacking pre-trained weights. Thus, we are utilizing their re-implementations instead of the original pre-trained weights.

Similarity - Demo

Face recognition models are regular convolutional neural networks and they are responsible to represent faces as vectors. We expect that a face pair of same person should be more similar than a face pair of different persons.

Similarity could be calculated by different metrics such as Cosine Similarity, Euclidean Distance or L2 normalized Euclidean. The default configuration uses cosine similarity. According to experiments, no distance metric is overperforming than other.

Python

metrics = ["cosine", "euclidean", "euclidean_l2"]

#face verification
result = DeepFace.verify(
  img1_path = "img1.jpg", 
  img2_path = "img2.jpg", 
  distance_metric = metrics[1],
)

#face recognition
dfs = DeepFace.find(
  img_path = "img1.jpg", 
  db_path = "C:/workspace/my_db", 
  distance_metric = metrics[2],
)
Facial Attribute Analysis - Demo

DeepFace also comes with a strong facial attribute analysis module including age, gender, facial expression (including angry, fear, neutral, sad, disgust, happy and surprise) and race (including asian, white, middle eastern, indian, latino and black) predictions. Result is going to be the size of faces appearing in the source image.

Python

objs = DeepFace.analyze(
  img_path = "img4.jpg", 
  actions = ['age', 'gender', 'race', 'emotion'],
)
https://raw.githubusercontent.com/serengil/deepface/master/icon/stock-2.jpg" width="95%" height="95%">

Age model got ± 4.65 MAE; gender model got 97.44% accuracy, 96.29% precision and 95.05% recall as mentioned in its tutorial.

Face Detection and Alignment - Demo

Face detection and alignment are important early stages of a modern face recognition pipeline. Experiments show that detection increases the face recognition accuracy up to 42%, while alignment increases it up to 6%. OpenCV, Ssd, Dlib,  MtCnn, Faster MtCnn, RetinaFace, MediaPipe, Yolo, YuNet and CenterFace detectors are wrapped in deepface.

https://raw.githubusercontent.com/serengil/deepface/master/icon/detector-portfolio-v6.jpg" width="95%" height="95%">

All deepface functions accept optional detector backend and align input arguments. You can switch among those detectors and alignment modes with these arguments. OpenCV is the default detector and alignment is on by default.

Python

backends = [
  'opencv', 
  'ssd', 
  'dlib', 
  'mtcnn', 
  'fastmtcnn',
  'retinaface', 
  'mediapipe',
  'yolov8',
  'yunet',
  'centerface',
]

alignment_modes = [True, False]

#face verification
obj = DeepFace.verify(
  img1_path = "img1.jpg", 
  img2_path = "img2.jpg", 
  detector_backend = backends[0],
  align = alignment_modes[0],
)

#face recognition
dfs = DeepFace.find(
  img_path = "img.jpg", 
  db_path = "my_db", 
  detector_backend = backends[1],
  align = alignment_modes[0],
)

#embeddings
embedding_objs = DeepFace.represent(
  img_path = "img.jpg", 
  detector_backend = backends[2],
  align = alignment_modes[0],
)

#facial analysis
demographies = DeepFace.analyze(
  img_path = "img4.jpg", 
  detector_backend = backends[3],
  align = alignment_modes[0],
)

#face detection and alignment
face_objs = DeepFace.extract_faces(
  img_path = "img.jpg", 
  detector_backend = backends[4],
  align = alignment_modes[0],
)
Face recognition models are actually CNN models and they expect standard sized inputs. So, resizing is required before representation. To avoid deformation, deepface adds black padding pixels according to the target size argument after detection and alignment.

https://raw.githubusercontent.com/serengil/deepface/master/icon/detector-outputs-20240414.jpg" width="90%" height="90%">

RetinaFace and MtCnn seem to overperform in detection and alignment stages but they are much slower. If the speed of your pipeline is more important, then you should use opencv or ssd. On the other hand, if you consider the accuracy, then you should use retinaface or mtcnn.

The performance of RetinaFace is very satisfactory even in the crowd as seen in the following illustration. Besides, it comes with an incredible facial landmark detection performance. Highlighted red points show some facial landmarks such as eyes, nose and mouth. That's why, alignment score of RetinaFace is high as well.

https://raw.githubusercontent.com/serengil/deepface/master/icon/retinaface-results.jpeg" width="90%" height="90%">

Achmad Roychan, Moh Taufik Febriansah, Andika Arif Sofyan

You can find out more about RetinaFace on this repo.

Real Time Analysis - Demo

You can run deepface for real time videos as well. Stream function will access your webcam and apply both face recognition and facial attribute analysis. The function starts to analyze a frame if it can focus a face sequentially 5 frames. Then, it shows results 5 seconds.

Python

DeepFace.stream(db_path = "C:/User/Sefik/Desktop/database")
https://raw.githubusercontent.com/serengil/deepface/master/icon/stock-3.jpg" width="90%" height="90%">

Even though face recognition is based on one-shot learning, you can use multiple face pictures of a person as well. You should rearrange your directory structure as illustrated below.

Bash

user
├── database
│   ├── Alice
│   │   ├── Alice1.jpg
│   │   ├── Alice2.jpg
│   ├── Bob
│   │   ├── Bob.jpg
React UI - Demo part-i, Demo part-ii

If you intend to perform face verification tasks directly from your browser, deepface-react-ui is a separate repository built using ReactJS depending on deepface api.

https://raw.githubusercontent.com/serengil/deepface/master/icon/deepface-and-react.jpg" width="90%" height="90%">

Face Anti Spoofing - Demo

DeepFace also includes an anti-spoofing analysis module to understand given image is real or fake. To activate this feature, set the anti_spoofing argument to True in any DeepFace tasks.

https://raw.githubusercontent.com/serengil/deepface/master/icon/face-anti-spoofing.jpg" width="40%" height="40%">

Python

# anti spoofing test in face detection
face_objs = DeepFace.extract_faces(
  img_path="dataset/img1.jpg",
  anti_spoofing = True
)
assert all(face_obj["is_real"] is True for face_obj in face_objs)

# anti spoofing test in real time analysis
DeepFace.stream(
  db_path = "C:/User/Sefik/Desktop/database",
  anti_spoofing = True
)
API - Demo

DeepFace serves an API as well - see api folder for more details. You can clone deepface source code and run the api with the following command. It will use gunicorn server to get a rest service up. In this way, you can call deepface from an external system such as mobile app or web.

Shell

cd scripts
./service.sh
https://raw.githubusercontent.com/serengil/deepface/master/icon/deepface-api.jpg" width="90%" height="90%">

Face recognition, facial attribute analysis and vector representation functions are covered in the API. You are expected to call these functions as http post methods. Default service endpoints will be http://localhost:5005/verify for face recognition, http://localhost:5005/analyze for facial attribute analysis, and http://localhost:5005/represent for vector representation. You can pass input images as exact image paths on your environment, base64 encoded strings or images on web. Here, you can find a postman project to find out how these methods should be called.

Dockerized Service - Demo

The following command set will serve deepface on localhost:5005 via docker. Then, you will be able to consume deepface services such as verify, analyze and represent. Also, if you want to build the image by your own instead of pre-built image from docker hub, Dockerfile is available in the root folder of the project.

Shell

# docker build -t serengil/deepface . # build docker image from Dockerfile
docker pull serengil/deepface # use pre-built docker image from docker hub
docker run -p 5005:5000 serengil/deepface
https://raw.githubusercontent.com/serengil/deepface/master/icon/deepface-dockerized-v2.jpg" width="50%" height="50%">

Command Line Interface - Demo

DeepFace comes with a command line interface as well. You are able to access its functions in command line as shown below. The command deepface expects the function name as 1st argument and function arguments thereafter.

Shell

#face verification
$ deepface verify -img1_path tests/dataset/img1.jpg -img2_path tests/dataset/img2.jpg

#facial analysis
$ deepface analyze -img_path tests/dataset/img1.jpg
You can also run these commands if you are running deepface with docker. Please follow the instructions in the shell script.

Large Scale Facial Recognition - Playlist

If your task requires facial recognition on large datasets, you should combine DeepFace with a vector index or vector database. This setup will perform approximate nearest neighbor searches instead of exact ones, allowing you to identify a face in a database containing billions of entries within milliseconds. Common vector index solutions include Annoy, Faiss, Voyager, NMSLIB, ElasticSearch. For vector databases, popular options are Postgres with its pgvector extension and RediSearch.

https://raw.githubusercontent.com/serengil/deepface/master/icon/deepface-big-data.jpg" width="90%" height="90%">

Conversely, if your task involves facial recognition on small to moderate-sized databases, you can adopt use relational databases such as Postgres or SQLite, or NoSQL databases like Mongo, Redis or Cassandra to perform exact nearest neighbor search.

Contribution
Pull requests are more than welcome! If you are planning to contribute a large patch, please create an issue first to get any upfront questions or design decisions out of the way first.

Before creating a PR, you should run the unit tests and linting locally by running make test && make lint command. Once a PR sent, GitHub test workflow will be run automatically and unit test and linting jobs will be available in GitHub actions before approval.

Support
There are many ways to support a project - starring⭐️ the GitHub repo is just one 🙏

If you do like this work, then you can support it financially on Patreon, GitHub Sponsors or Buy Me a Coffee.

https://www.patreon.com/serengil?repo=deepface">
https://raw.githubusercontent.com/serengil/deepface/master/icon/patreon.png" width="30%" height="30%">

https://buymeacoffee.com/serengil">
https://raw.githubusercontent.com/serengil/deepface/master/icon/bmc-button.png" width="25%" height="25%">

Also, your company's logo will be shown on README on GitHub if you become a sponsor in gold, silver or bronze tiers.

Citation
Please cite deepface in your publications if it helps your research - see CITATIONS for more details. Here are its BibTex entries:

If you use deepface in your research for facial recogntion or face detection purposes, please cite these publications:

Code snippet

@inproceedings{serengil2020lightface,
  title          = {LightFace: A Hybrid Deep Face Recognition Framework},
  author         = {Serengil, Sefik Ilkin and Ozpinar, Alper},
  booktitle      = {2020 Innovations in Intelligent Systems and Applications Conference (ASYU)},
  pages          = {23-27},
  year           = {2020},
  doi            = {10.1109/ASYU50717.2020.9259802},
  url            = {https://ieeexplore.ieee.org/document/9259802},
  organization = {IEEE}
}
Code snippet

@article{serengil2024lightface,
  title     = {A Benchmark of Facial Recognition Pipelines and Co-Usability Performances of Modules},
  author    = {Serengil, Sefik and Ozpinar, Alper},
  journal   = {Journal of Information Technologies},
  volume    = {17},
  number    = {2},
  pages     = {95-107},
  year      = {2024},
  doi       = {10.17671/gazibtd.1399077},
  url       = {https://dergipark.org.tr/en/pub/gazibtd/issue/84331/1399077},
  publisher = {Gazi University}
}
On the other hand, if you use deepface in your research for facial attribute analysis purposes such as age, gender, emotion or ethnicity prediction tasks, please cite this publication.

Code snippet

@inproceedings{serengil2021lightface,
  title          = {HyperExtended LightFace: A Facial Attribute Analysis Framework},
  author         = {Serengil, Sefik Ilkin and Ozpinar, Alper},
  booktitle      = {2021 International Conference on Engineering and Emerging Technologies (ICEET)},
  pages          = {1-4},
  year           = {2021},
  doi            = {10.1109/ICEET53442.2021.9659697},
  url            = {https://ieeexplore.ieee.org/document/9659697},
  organization = {IEEE}
}
Also, if you use deepface in your GitHub projects, please add deepface in the requirements.txt.
## Licence

DeepFace is licensed under the MIT License - see [`LICENSE`](https://github.com/serengil/deepface/blob/master/LICENSE) for more details.

DeepFace wraps some external face recognition models: [VGG-Face](http://www.robots.ox.ac.uk/~vgg/software/vgg_face/), [Facenet](https://github.com/davidsandberg/facenet/blob/master/LICENSE.md) (both 128d and 512d), [OpenFace](https://github.com/iwantooxxoox/Keras-OpenFace/blob/master/LICENSE), [DeepFace](https://github.com/swghosh/DeepFace), [DeepID](https://github.com/Ruoyiran/DeepID/blob/master/LICENSE.md), [ArcFace](https://github.com/leondgarse/Keras_insightface/blob/master/LICENSE), [Dlib](https://github.com/davisking/dlib/blob/master/dlib/LICENSE.txt), [SFace](https://github.com/opencv/opencv_zoo/blob/master/models/face_recognition_sface/LICENSE) and [GhostFaceNet](https://github.com/HamadYA/GhostFaceNets/blob/main/LICENSE). Besides, age, gender and race / ethnicity models were trained on the backbone of VGG-Face with transfer learning. Similarly, DeepFace wraps many face detectors: [OpenCv](https://github.com/opencv/opencv/blob/4.x/LICENSE), [Ssd](https://github.com/opencv/opencv/blob/master/LICENSE), [Dlib](https://github.com/davisking/dlib/blob/master/LICENSE.txt), [MtCnn](https://github.com/ipazc/mtcnn/blob/master/LICENSE), [Fast MtCnn](https://github.com/timesler/facenet-pytorch/blob/master/LICENSE.md), [RetinaFace](https://github.com/serengil/retinaface/blob/master/LICENSE), [MediaPipe](https://github.com/google/mediapipe/blob/master/LICENSE), [YuNet](https://github.com/ShiqiYu/libfacedetection/blob/master/LICENSE), [Yolo](https://github.com/derronqi/yolov8-face/blob/main/LICENSE) and [CenterFace](https://github.com/Star-Clouds/CenterFace/blob/master/LICENSE). Finally, DeepFace is optionally using [face anti spoofing](https://github.com/minivision-ai/Silent-Face-Anti-Spoofing/blob/master/LICENSE) to determine the given images are real or fake. License types will be inherited when you intend to utilize those models. Please check the license types of those models for production purposes.

DeepFace [logo](https://thenounproject.com/term/face-recognition/2965879/) is created by [Adrien Coquet](https://thenounproject.com/coquet_adrien/) and it is licensed under [Creative Commons: By Attribution 3.0 License](https://creativecommons.org/licenses/by/3.0/).


