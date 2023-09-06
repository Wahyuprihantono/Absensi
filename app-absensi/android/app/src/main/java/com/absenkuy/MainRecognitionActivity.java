package com.absenkuy;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.content.res.AssetFileDescriptor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.ImageFormat;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.RectF;
import android.graphics.YuvImage;
import android.media.Image;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;

import androidx.annotation.Keep;
import androidx.annotation.NonNull;

import androidx.annotation.RequiresApi;
import androidx.camera.core.CameraSelector;
import androidx.camera.core.ImageAnalysis;
import androidx.camera.core.ImageProxy;
import androidx.camera.core.Preview;
import androidx.camera.lifecycle.ProcessCameraProvider;

import com.absenkuy.Recognition.SimilarityClassifier;
import com.facebook.react.ReactActivity;
import com.facebook.react.bridge.Arguments;
import com.facebook.react.bridge.ReactContext;
import com.facebook.react.bridge.ReadableArray;
import com.facebook.react.bridge.WritableMap;
import com.facebook.react.modules.core.DeviceEventManagerModule;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;
import com.google.common.util.concurrent.ListenableFuture;


import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import com.google.mlkit.vision.common.InputImage;
import com.google.mlkit.vision.face.Face;
import com.google.mlkit.vision.face.FaceDetection;
import com.google.mlkit.vision.face.FaceDetector;
import com.google.mlkit.vision.face.FaceDetectorOptions;

import com.absenkuy.Helper.GraphicOverlay;
import com.absenkuy.Helper.RectOverlay;
import com.absenkuy.Rest.ApiClient;
import com.absenkuy.Rest.FaceModelRequest;
import com.absenkuy.Rest.FaceModelResponse;

import androidx.camera.view.PreviewView;
import androidx.core.content.ContextCompat;
import androidx.lifecycle.LifecycleOwner;

import android.os.ParcelFileDescriptor;
import android.util.Base64;
import android.util.Pair;
import android.util.Size;
import android.view.View;

import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import org.tensorflow.lite.Interpreter;

import java.io.ByteArrayOutputStream;
import java.io.FileDescriptor;
import java.io.FileInputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.ByteOrder;
import java.nio.MappedByteBuffer;
import java.nio.ReadOnlyBufferException;
import java.nio.channels.FileChannel;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

@Keep
public class MainRecognitionActivity extends ReactActivity {
    public ListenableFuture<ProcessCameraProvider> cameraProviderFuture;
    public HashMap<String, SimilarityClassifier.Recognition> registered = new HashMap<>(); //saved Faces
    public static final int MY_CAMERA_REQUEST_CODE = 100;
    public static int SELECT_PICTURE = 1;

    public CameraSelector cameraSelector;
    public FaceDetector detector;
    public Interpreter tfLite;
    public ProcessCameraProvider cameraProvider;
    public GraphicOverlay graphicOverlay;

    public Context context = MainRecognitionActivity.this;
    public Context activityContext;
    public ReactContext reactContext;
    public TextView reco_name,preview_info;
    public Button recognize;
    public ImageButton add_face;
    public PreviewView previewView;
    public FrameLayout frameLayout;
    public RectOverlay rectOverlay;

    public TextView textTitle;
    public TextView textSubTitle;
    public Boolean training=false;

    int cam_face=cameraSelector.LENS_FACING_FRONT; //Default Back Camera
    int inputSize=112;  //Input size for model
    int[] intValues;
    int OUTPUT_SIZE=192; //Output size of model

    public boolean start=true;
    boolean flipX=false;
    boolean isVerified=false;
    boolean isModelQuantized=false;

    private float[][] embeedings;
    float IMAGE_MEAN = 128.0f;
    float IMAGE_STD = 128.0f;
    private float distance = 2.0f;

    public String faceKey=null;
    public String faceValue=null;
    public String modelFile="mobile_face_net.tflite"; //model name
    public String LableSuccess = "ABSEN SEKARANG";
    public String LableError = "WAJAH TIDAK SESUAI";
    public String uri;
    public String access_token;
    public String user_name;
    public Bitmap frame_bmp;
    public Bitmap cropped_face;
    public Bitmap scaled;

    @RequiresApi(api = Build.VERSION_CODES.M)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
//        registered.clear();
//        getFaceModel();
        setContentView(R.layout.activity_main);

        recognize= (Button) findViewById(R.id.recognize);
        frameLayout = (FrameLayout) findViewById(R.id.frameLayout);
        previewView = (PreviewView) findViewById(R.id.previewView);
        graphicOverlay = (GraphicOverlay) findViewById(R.id.rectOverlay);
        textTitle = (TextView) findViewById(R.id.textTitle);
        textSubTitle = (TextView) findViewById(R.id.textSubTitle);

        recognize.setClickable(false);
        graphicOverlay.setCameraInfo(1280,720,cam_face);

        SharedPreferences mSettings = getSharedPreferences("HashMap", Context.MODE_PRIVATE);
        access_token = "Bearer "+mSettings.getString("access_token", null);
        user_name = mSettings.getString("name", null);
        //Camera Permission
        if (checkSelfPermission(Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            requestPermissions(new String[]{Manifest.permission.CAMERA}, MY_CAMERA_REQUEST_CODE);
        }
        cam_face = CameraSelector.LENS_FACING_FRONT;
        recognize.setOnClickListener((new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                MainRecognitionActivity.this.finish();
                onLoading(true);
                if (training){

                    SimilarityClassifier.Recognition result = new SimilarityClassifier.Recognition(
                            "0", "", -1f);
                    result.setExtra(embeedings);
                    if (registered.size() > 0){
                        registered.remove(faceKey);
                    }
                    uri = getBitmapToUri(scaled);
                    faceKey = user_name;
                    Toast.makeText(MainRecognitionActivity.this,user_name, Toast.LENGTH_LONG).show();
                    registered.put(user_name,result);
                    insertToSP(registered,false);
                }else{
                    uri = getBitmapToUri(scaled);
                    onSuccess(true);
                }
//



            }
        }));

        //Load model
        try {
            tfLite=new Interpreter(loadModelFile(MainRecognitionActivity.this,modelFile));
        } catch (IOException e) {
            e.printStackTrace();
        }
        //Initialize Face Detector
        FaceDetectorOptions highAccuracyOpts =
                new FaceDetectorOptions.Builder()
                        .setPerformanceMode(FaceDetectorOptions.PERFORMANCE_MODE_ACCURATE)
                        .build();
        detector = FaceDetection.getClient(highAccuracyOpts);

        cameraBind();

    }


    public FaceModelRequest CreateModelRequest(String key, String value){
        FaceModelRequest faceModelRequest = new FaceModelRequest();
        faceModelRequest.setKey(key);
        faceModelRequest.setValue(value);
        faceModelRequest.setFace_photo(uri);

        return faceModelRequest;
    }


    public void saveFaceModel(FaceModelRequest faceModelRequest){
        Call<FaceModelResponse> userResponseCall = ApiClient.getFaceModelService().saveFaceModel(access_token,faceModelRequest);
        userResponseCall.enqueue(new Callback<FaceModelResponse>() {
            @Override
            public void onResponse(Call<FaceModelResponse> call, Response<FaceModelResponse> response) {
                if (response.isSuccessful()){
                    onSuccess(true);
                    Toast.makeText(MainRecognitionActivity.this,"Data berhasil disimpan", Toast.LENGTH_LONG).show();
                }else{
                    onSuccess(false);
                    Toast.makeText(MainRecognitionActivity.this,"Request Gagal"+response.toString(), Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<FaceModelResponse> call, Throwable t) {
                onSuccess(false);
                Toast.makeText(MainRecognitionActivity.this,"Data gagal disimpan"+t.getLocalizedMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    public void getFaceModel(){
        Call<FaceModelResponse> userResponseCall = ApiClient.getFaceModelService().retrieveFaceModel(access_token);
        userResponseCall.enqueue(new Callback<FaceModelResponse>() {
            @Override
            public void onResponse(Call<FaceModelResponse> call, Response<FaceModelResponse> response) {
                if (response.isSuccessful()){
                    faceKey = response.body().getKey();
                    faceValue = response.body().getValue();
                    registered=readFromSP(); //Load saved faces from memory when app starts
                    System.out.println("registered"+response.toString());
                }else{
                    faceKey = null;
                    faceValue = null;
                    Toast.makeText(MainRecognitionActivity.this,"Terjadi masalah"+response.toString(), Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<FaceModelResponse> call, Throwable t) {
                faceKey = null;
                faceValue = null;
                Toast.makeText(MainRecognitionActivity.this,"Terjadi masalah"+t.getLocalizedMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }


    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == MY_CAMERA_REQUEST_CODE) {
            if (grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Toast.makeText(this, "camera permission granted", Toast.LENGTH_LONG).show();
            } else {
                Toast.makeText(this, "camera permission denied", Toast.LENGTH_LONG).show();
            }
        }
    }

    public MappedByteBuffer loadModelFile(Activity activity, String MODEL_FILE) throws IOException {
        AssetFileDescriptor fileDescriptor = activity.getAssets().openFd(MODEL_FILE);
        FileInputStream inputStream = new FileInputStream(fileDescriptor.getFileDescriptor());
        FileChannel fileChannel = inputStream.getChannel();
        long startOffset = fileDescriptor.getStartOffset();
        long declaredLength = fileDescriptor.getDeclaredLength();
        return fileChannel.map(FileChannel.MapMode.READ_ONLY, startOffset, declaredLength);
    }

    //Bind camera and preview view
    private void cameraBind()
    {
        cameraProviderFuture = ProcessCameraProvider.getInstance(this);

//        previewView=findViewById(R.id.previewView);
        cameraProviderFuture.addListener(() -> {
            try {
                cameraProvider = cameraProviderFuture.get();

                bindPreview(cameraProvider);
            } catch (ExecutionException | InterruptedException e) {
                // No errors need to be handled for this in Future.
                // This should never be reached.
            }
        }, ContextCompat.getMainExecutor(this));
    }
    void bindPreview(@NonNull ProcessCameraProvider cameraProvider) {
        Preview preview = new Preview.Builder()
                .build();

        cameraSelector = new CameraSelector.Builder()
                .requireLensFacing(cam_face)
                .build();

        preview.setSurfaceProvider(previewView.getSurfaceProvider());
        ImageAnalysis imageAnalysis =
                new ImageAnalysis.Builder()
                        .setTargetResolution(new Size(1280, 720))
                        .setBackpressureStrategy(ImageAnalysis.STRATEGY_KEEP_ONLY_LATEST) //Latest frame is shown
                        .build();

        Executor executor = Executors.newSingleThreadExecutor();
        imageAnalysis.setAnalyzer(executor, new ImageAnalysis.Analyzer() {
            @Override
            public void analyze(@NonNull ImageProxy imageProxy) {

                InputImage image = null;


                @SuppressLint("UnsafeExperimentalUsageError")
                // Camera Feed-->Analyzer-->ImageProxy-->mediaImage-->InputImage(needed for ML kit face detection)

                        Image mediaImage = imageProxy.getImage();

                if (mediaImage != null) {
                    image = InputImage.fromMediaImage(mediaImage, imageProxy.getImageInfo().getRotationDegrees());
                }

                //Process acquired image to detect faces
                Task<List<Face>> result =
                        detector.process(image)
                                .addOnSuccessListener(
                                        new OnSuccessListener<List<Face>>() {
                                            @Override
                                            public void onSuccess(List<Face> faces) {

//                                                System.out.println("Wajah tidak terdeteksi");
                                                recognize.setText("TIDAK ADA WAJAH DIKAMERA");
                                                recognize.setClickable(false);
                                                recognize.setBackgroundColor(Color.parseColor("#8395a7"));
                                                graphicOverlay.remove(rectOverlay);
                                                if(faces.size()!=0) {
                                                    graphicOverlay.remove(rectOverlay);
                                                    Face face = faces.get(0); //Get first face from detected faces
//                                                    System.out.println(face);

                                                    //mediaImage to Bitmap
                                                    frame_bmp = toBitmap(mediaImage);
                                                    int rot = imageProxy.getImageInfo().getRotationDegrees();

                                                    if(training == false){
//                                                        uri = getBitmapToUri(MainRecognitionActivity.this, frame_bmp);
                                                    }


                                                    recognize.setClickable(true);
                                                    //Adjust orientation of Face
                                                    Bitmap frame_bmp1 = rotateBitmap(frame_bmp, rot, flipX, false);

                                                    //Get bounding box of face
                                                    RectF boundingBox = new RectF(face.getBoundingBox());
                                                    rectOverlay = new RectOverlay(graphicOverlay, face.getBoundingBox());
                                                    graphicOverlay.add(rectOverlay);


                                                    //Crop out bounding box from whole Bitmap(image)
                                                    cropped_face = getCropBitmapByCPU(frame_bmp1, boundingBox);
                                                    if (training){
                                                        recognize.setText(LableSuccess);
                                                        recognize.setBackgroundColor(Color.parseColor("#273c75"));
//                                                        uri = getBitmapToUri(cropped_face);
                                                    }


                                                    //Scale the acquired Face to 112*112 which is required input for model
                                                    scaled = getResizedBitmap(cropped_face, 112, 112);



                                                    if(start)
                                                        recognizeImage(scaled); //Send scaled bitmap to create face embeddings.
                                                    try {
                                                        Thread.sleep(100);  //Camera preview refreshed every 100 millisec(adjust as required)
                                                    } catch (InterruptedException e) {
                                                        e.printStackTrace();
                                                    }

                                                }

                                            }
                                        })
                                .addOnFailureListener(
                                        new OnFailureListener() {
                                            @Override
                                            public void onFailure(@NonNull Exception e) {
                                                // Task failed with an exception
                                                // ...
                                            }
                                        })
                                .addOnCompleteListener(new OnCompleteListener<List<Face>>() {
                                    @Override
                                    public void onComplete(@NonNull Task<List<Face>> task) {

                                        imageProxy.close(); //v.important to acquire next frame for analysis
                                    }
                                });


            }
        });


        cameraProvider.bindToLifecycle((LifecycleOwner) this, cameraSelector, imageAnalysis, preview);


    }

    private void recognizeImage(final Bitmap bitmap) {

        //Create ByteBuffer to store normalized image

        ByteBuffer imgData = ByteBuffer.allocateDirect(1 * inputSize * inputSize * 3 * 4);

        imgData.order(ByteOrder.nativeOrder());

        intValues = new int[inputSize * inputSize];

        //get pixel values from Bitmap to normalize
        bitmap.getPixels(intValues, 0, bitmap.getWidth(), 0, 0, bitmap.getWidth(), bitmap.getHeight());

        imgData.rewind();

        for (int i = 0; i < inputSize; ++i) {
            for (int j = 0; j < inputSize; ++j) {
                int pixelValue = intValues[i * inputSize + j];
                if (isModelQuantized) {
                    // Quantized model
                    imgData.put((byte) ((pixelValue >> 16) & 0xFF));
                    imgData.put((byte) ((pixelValue >> 8) & 0xFF));
                    imgData.put((byte) (pixelValue & 0xFF));
                } else { // Float model
                    imgData.putFloat((((pixelValue >> 16) & 0xFF) - IMAGE_MEAN) / IMAGE_STD);
                    imgData.putFloat((((pixelValue >> 8) & 0xFF) - IMAGE_MEAN) / IMAGE_STD);
                    imgData.putFloat(((pixelValue & 0xFF) - IMAGE_MEAN) / IMAGE_STD);

                }
            }
        }
        //imgData is input to our model
        Object[] inputArray = {imgData};

        Map<Integer, Object> outputMap = new HashMap<>();


        embeedings = new float[1][OUTPUT_SIZE]; //output of model will be stored in this variable

        outputMap.put(0, embeedings);

        tfLite.runForMultipleInputsOutputs(inputArray, outputMap); //Run model



        float distance = Float.MAX_VALUE;
        String id = "0";
        String label = "?";

        //Compare new face with saved Faces.
        if (training == false){
            if (registered.size() > 0) {

                final Pair<String, Float> nearest = findNearest(embeedings[0]);//Find closest matching face

                if (nearest != null) {

                    final String name = nearest.first;
                    label = name;
                    distance = nearest.second;
                    if(distance<1.000f){
                        recognize.setClickable(true);
                        recognize.setText(LableSuccess);
                        recognize.setBackgroundColor(Color.parseColor("#273c75"));

                    }else{
                        recognize.setClickable(false);
                        recognize.setText(LableError);
                        recognize.setBackgroundColor(Color.parseColor("#8395a7"));
                    }
                }
            }
        }

    }


    //Compare Faces by distance between face embeddings
    private Pair<String, Float> findNearest(float[] emb) {

        Pair<String, Float> ret = null;
        for (Map.Entry<String, SimilarityClassifier.Recognition> entry : registered.entrySet()) {

            final String name = entry.getKey();
            final float[] knownEmb = ((float[][]) entry.getValue().getExtra())[0];

            float distance = 0;
            for (int i = 0; i < emb.length; i++) {
                float diff = emb[i] - knownEmb[i];
                distance += diff*diff;
            }
            distance = (float) Math.sqrt(distance);
            if (ret == null || distance < ret.second) {
                ret = new Pair<>(name, distance);
            }
        }

        return ret;

    }
    public Bitmap getResizedBitmap(Bitmap bm, int newWidth, int newHeight) {
        int width = bm.getWidth();
        int height = bm.getHeight();
        float scaleWidth = ((float) newWidth) / width;
        float scaleHeight = ((float) newHeight) / height;
        // CREATE A MATRIX FOR THE MANIPULATION
        Matrix matrix = new Matrix();
        // RESIZE THE BIT MAP
        matrix.postScale(scaleWidth, scaleHeight);

        // "RECREATE" THE NEW BITMAP
        Bitmap resizedBitmap = Bitmap.createBitmap(
                bm, 0, 0, width, height, matrix, false);
        bm.recycle();
        return resizedBitmap;
    }
    private static Bitmap getCropBitmapByCPU(Bitmap source, RectF cropRectF) {
        Bitmap resultBitmap = Bitmap.createBitmap((int) cropRectF.width(),
                (int) cropRectF.height(), Bitmap.Config.ARGB_8888);
        Canvas cavas = new Canvas(resultBitmap);

        // draw background
        Paint paint = new Paint(Paint.FILTER_BITMAP_FLAG);
        paint.setColor(Color.WHITE);
        cavas.drawRect(//from  w w  w. ja v  a  2s. c  om
                new RectF(0, 0, cropRectF.width(), cropRectF.height()),
                paint);

        Matrix matrix = new Matrix();
        matrix.postTranslate(-cropRectF.left, -cropRectF.top);

        cavas.drawBitmap(source, matrix, paint);

        if (source != null && !source.isRecycled()) {
            source.recycle();
        }

        return resultBitmap;
    }

    public static Bitmap rotateBitmap(Bitmap bitmap, int rotationDegrees, boolean flipX, boolean flipY) {
        Matrix matrix = new Matrix();

        // Rotate the image back to straight.
        matrix.postRotate(rotationDegrees);

        // Mirror the image along the X or Y axis.
        matrix.postScale(flipX ? -1.0f : 1.0f, flipY ? -1.0f : 1.0f);
        Bitmap rotatedBitmap =
                Bitmap.createBitmap(bitmap, 0, 0, bitmap.getWidth(), bitmap.getHeight(), matrix, true);

        // Recycle the old bitmap if it has changed.
        if (rotatedBitmap != bitmap) {
            bitmap.recycle();
        }
        return rotatedBitmap;
    }

    //IMPORTANT. If conversion not done ,the toBitmap conversion does not work on some devices.
    private static byte[] YUV_420_888toNV21(Image image) {

        int width = image.getWidth();
        int height = image.getHeight();
        int ySize = width*height;
        int uvSize = width*height/4;

        byte[] nv21 = new byte[ySize + uvSize*2];

        ByteBuffer yBuffer = image.getPlanes()[0].getBuffer(); // Y
        ByteBuffer uBuffer = image.getPlanes()[1].getBuffer(); // U
        ByteBuffer vBuffer = image.getPlanes()[2].getBuffer(); // V

        int rowStride = image.getPlanes()[0].getRowStride();
        assert(image.getPlanes()[0].getPixelStride() == 1);

        int pos = 0;

        if (rowStride == width) { // likely
            yBuffer.get(nv21, 0, ySize);
            pos += ySize;
        }
        else {
            long yBufferPos = -rowStride; // not an actual position
            for (; pos<ySize; pos+=width) {
                yBufferPos += rowStride;
                yBuffer.position((int) yBufferPos);
                yBuffer.get(nv21, pos, width);
            }
        }

        rowStride = image.getPlanes()[2].getRowStride();
        int pixelStride = image.getPlanes()[2].getPixelStride();

        assert(rowStride == image.getPlanes()[1].getRowStride());
        assert(pixelStride == image.getPlanes()[1].getPixelStride());

        if (pixelStride == 2 && rowStride == width && uBuffer.get(0) == vBuffer.get(1)) {
            // maybe V an U planes overlap as per NV21, which means vBuffer[1] is alias of uBuffer[0]
            byte savePixel = vBuffer.get(1);
            try {
                vBuffer.put(1, (byte)~savePixel);
                if (uBuffer.get(0) == (byte)~savePixel) {
                    vBuffer.put(1, savePixel);
                    vBuffer.position(0);
                    uBuffer.position(0);
                    vBuffer.get(nv21, ySize, 1);
                    uBuffer.get(nv21, ySize + 1, uBuffer.remaining());

                    return nv21; // shortcut
                }
            }
            catch (ReadOnlyBufferException ex) {
                // unfortunately, we cannot check if vBuffer and uBuffer overlap
            }

            // unfortunately, the check failed. We must save U and V pixel by pixel
            vBuffer.put(1, savePixel);
        }

        // other optimizations could check if (pixelStride == 1) or (pixelStride == 2),
        // but performance gain would be less significant

        for (int row=0; row<height/2; row++) {
            for (int col=0; col<width/2; col++) {
                int vuPos = col*pixelStride + row*rowStride;
                nv21[pos++] = vBuffer.get(vuPos);
                nv21[pos++] = uBuffer.get(vuPos);
            }
        }

        return nv21;
    }

    private Bitmap toBitmap(Image image) {

        byte[] nv21=YUV_420_888toNV21(image);


        YuvImage yuvImage = new YuvImage(nv21, ImageFormat.NV21, image.getWidth(), image.getHeight(), null);

        ByteArrayOutputStream out = new ByteArrayOutputStream();
        yuvImage.compressToJpeg(new Rect(0, 0, yuvImage.getWidth(), yuvImage.getHeight()), 75, out);

        byte[] imageBytes = out.toByteArray();
        //System.out.println("bytes"+ Arrays.toString(imageBytes));

        //System.out.println("FORMAT"+image.getFormat());

        return BitmapFactory.decodeByteArray(imageBytes, 0, imageBytes.length);
    }

    //Save Faces to Shared Preferences.Conversion of Recognition objects to json string
    private void insertToSP(HashMap<String, SimilarityClassifier.Recognition> jsonMap,boolean clear) {
        if(clear)
            jsonMap.clear();
        else
            jsonMap.putAll(readFromSP());
        String jsonString = new Gson().toJson(jsonMap);
//        System.out.println("Input josn"+jsonString.toString());
        SharedPreferences sharedPreferences = getSharedPreferences("HashMap", MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString("map", jsonString);
//        System.out.println("Input josn"+jsonString.toString());
        editor.apply();
        saveFaceModel(CreateModelRequest(user_name,jsonString.toString()));

        Toast.makeText(context, "Recognitions Saved", Toast.LENGTH_SHORT).show();
    }

    //Load Faces from Shared Preferences.Json String to Recognition object
    private HashMap<String, SimilarityClassifier.Recognition> readFromSP(){
        String json;
        if (faceValue == null){
            SharedPreferences sharedPreferences = getSharedPreferences("HashMap", MODE_PRIVATE);
            String defValue = new Gson().toJson(new HashMap<String, SimilarityClassifier.Recognition>());
            json= sharedPreferences.getString("map",defValue);
        }else{
            json = faceValue;
        }
        TypeToken<HashMap<String,SimilarityClassifier.Recognition>> token = new TypeToken<HashMap<String,SimilarityClassifier.Recognition>>() {};
        HashMap<String,SimilarityClassifier.Recognition> retrievedMap=new Gson().fromJson(json,token.getType());


        //During type conversion and save/load procedure,format changes(eg float converted to double).
        //So embeddings need to be extracted from it in required format(eg.double to float).
        for (Map.Entry<String, SimilarityClassifier.Recognition> entry : retrievedMap.entrySet())
        {
            float[][] output=new float[1][OUTPUT_SIZE];
            ArrayList arrayList= (ArrayList) entry.getValue().getExtra();
            arrayList = (ArrayList) arrayList.get(0);
            for (int counter = 0; counter < arrayList.size(); counter++) {
                output[0][counter]= ((Double) arrayList.get(counter)).floatValue();
            }
            entry.getValue().setExtra(output);
        }
        Toast.makeText(context, "Recognitions Loaded", Toast.LENGTH_SHORT).show();
        return retrievedMap;
    }

    //Similar Analyzing Procedure
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            if (requestCode == SELECT_PICTURE) {
                Uri selectedImageUri = data.getData();
                try {
                    InputImage impphoto=InputImage.fromBitmap(getBitmapFromUri(selectedImageUri),0);
                    detector.process(impphoto).addOnSuccessListener(new OnSuccessListener<List<Face>>() {
                        @Override
                        public void onSuccess(List<Face> faces) {

//                                                System.out.println("Wajah tidak terdeteksi");
                            recognize.setText("TIDAK ADA WAJAH DIKAMERA");
                            recognize.setClickable(false);
                            graphicOverlay.remove(rectOverlay);
                            if(faces.size()!=0) {
                                graphicOverlay.remove(rectOverlay);
                                Face face = faces.get(0); //Get first face from detected faces
//                                                    System.out.println(face);

                                //mediaImage to Bitmap
                                Bitmap frame_bmp= null;
                                try {
                                    frame_bmp = getBitmapFromUri(selectedImageUri);
                                } catch (IOException e) {
                                    e.printStackTrace();
                                }
                                Bitmap frame_bmp1 = rotateBitmap(frame_bmp, 0, flipX, false);
                                if(training == false){
                                    recognize.setText(LableSuccess);
                                    uri = getBitmapToUri(frame_bmp);
                                }
                                //Adjust orientation of Face

                                //Get bounding box of face
                                RectF boundingBox = new RectF(face.getBoundingBox());
                                rectOverlay = new RectOverlay(graphicOverlay, face.getBoundingBox());
                                graphicOverlay.add(rectOverlay);
                                if (training){
                                    recognize.setText(LableSuccess);
                                }
                                recognize.setClickable(true);

                                //Crop out bounding box from whole Bitmap(image)
                                Bitmap cropped_face = getCropBitmapByCPU(frame_bmp1, boundingBox);


                                //Scale the acquired Face to 112*112 which is required input for model
                                Bitmap scaled = getResizedBitmap(cropped_face, 112, 112);



                                if(training == false)
                                    recognizeImage(scaled); //Send scaled bitmap to create face embeddings.
                                try {
                                    Thread.sleep(100);  //Camera preview refreshed every 100 millisec(adjust as required)
                                } catch (InterruptedException e) {
                                    e.printStackTrace();
                                }

                            }

                        }
                    }).addOnFailureListener(new OnFailureListener() {
                        @Override
                        public void onFailure(@NonNull Exception e) {
                            start=true;
                            Toast.makeText(context, "Failed to add", Toast.LENGTH_SHORT).show();
                        }
                    });
//                    face_preview.setImageBitmap(getBitmapFromUri(selectedImageUri));
                } catch (IOException e) {
                    e.printStackTrace();
                }


            }
        }
    }

    private String getBitmapToUri(Bitmap inImage) {
        ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
        inImage.compress(Bitmap.CompressFormat.PNG, 100, byteArrayOutputStream);
        byte[] byteArray = byteArrayOutputStream .toByteArray();
        String encoded = Base64.encodeToString(byteArray, Base64.DEFAULT);
        return encoded;
    }

    private Bitmap getBitmapFromUri(Uri uri) throws IOException {
        ParcelFileDescriptor parcelFileDescriptor =
                getContentResolver().openFileDescriptor(uri, "r");
        FileDescriptor fileDescriptor = parcelFileDescriptor.getFileDescriptor();
        Bitmap image = BitmapFactory.decodeFileDescriptor(fileDescriptor);
        parcelFileDescriptor.close();
        return image;
    }

    public void onLoading(Boolean status) {
        ReactContext context = ((MainApplication)getApplication()).getReactNativeHost().getReactInstanceManager().getCurrentReactContext();

        if (context != null) {
            WritableMap params = Arguments.createMap();
            params.putBoolean("status",status);

            context.getJSModule(DeviceEventManagerModule.RCTDeviceEventEmitter.class)
                    .emit("onLoading", params);

        }
    }
    public void onSuccess(Boolean status) {
        ReactContext context = ((MainApplication)getApplication()).getReactNativeHost().getReactInstanceManager().getCurrentReactContext();

        if (context != null) {
            WritableMap params = Arguments.createMap();
            params.putBoolean("status",status);
            params.putString("uri", uri);

            context.getJSModule(DeviceEventManagerModule.RCTDeviceEventEmitter.class)
                    .emit("onSuccess", params);

        }
    }
}