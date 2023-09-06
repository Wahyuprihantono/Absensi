package com.absenkuy;

import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;

import androidx.annotation.RequiresApi;

public class TrainingActivity extends MainRecognitionActivity {

    @RequiresApi(api = Build.VERSION_CODES.M)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getFaceModel();
        training=true;
        recognize.setText("TIDAK ADA WAJAH DIKAMERA");
        recognize.setClickable(false);
        recognize.setBackgroundColor(Color.parseColor("#8395a7"));
        LableSuccess = "TRAINING";
        LableError = "TIDAK ADA WAJAH DIKAMERA";

        textTitle.setText("Pengenalan Wajah");
        textSubTitle.setText("Pastikan wajah kamu berada tepat didepan kamera");

        activityContext = TrainingActivity.this;

    }


}
