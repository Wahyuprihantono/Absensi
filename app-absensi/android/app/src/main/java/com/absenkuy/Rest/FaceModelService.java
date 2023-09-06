package com.absenkuy.Rest;

import android.content.Context;
import android.content.SharedPreferences;

import com.absenkuy.MainRecognitionActivity;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Headers;
import retrofit2.http.POST;

public interface FaceModelService {

    @POST("upload_face")
    Call<FaceModelResponse> saveFaceModel(@Header("Authorization") String access_token, @Body FaceModelRequest faceModelRequest);

    @GET("get_faces")
    Call<FaceModelResponse> retrieveFaceModel(@Header("Authorization") String access_token);
}
