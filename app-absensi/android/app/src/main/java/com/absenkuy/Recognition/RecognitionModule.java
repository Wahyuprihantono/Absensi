package com.absenkuy.Recognition;

import com.absenkuy.MainRecognitionActivity;
import com.absenkuy.RecognitionActivity;
import com.absenkuy.TrainingActivity;
import com.facebook.react.bridge.Callback;
import com.facebook.react.bridge.ReactApplicationContext;
import com.facebook.react.bridge.ReactContextBaseJavaModule;
import com.facebook.react.bridge.ReactMethod;

import android.content.Intent;
import android.util.Log;

public class RecognitionModule extends ReactContextBaseJavaModule {
    RecognitionModule(ReactApplicationContext context) {
        super(context);
    }

    @ReactMethod
    public void createCalendarEvent(String name, String location, Callback callBack) {
        Log.d("RecognitionModule", "Create event called with name: " + name
                + " and location: " + location);
                callBack.invoke("eventId");
    }

    @ReactMethod
    void navigateToRecognition() {
        ReactApplicationContext context = getReactApplicationContext();
        Intent intent = new Intent(context, RecognitionActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        context.startActivity(intent);
    }

    @ReactMethod
    void navigateToTrainingModel() {

        ReactApplicationContext context = getReactApplicationContext();
        Intent intent = new Intent(context, TrainingActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        context.startActivity(intent);
    }

    // add to RecognitionModule.java
    @Override
    public String getName() {
        return "RecognitionModule";
    }


}
