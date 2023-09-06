package com.absenkuy;

import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import androidx.annotation.RequiresApi;


public class RecognitionActivity extends MainRecognitionActivity {

    @RequiresApi(api = Build.VERSION_CODES.M)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getFaceModel();
        training = false;
        recognize.setText("TIDAK ADA WAJAH DIKAMERA");
        recognize.setClickable(false);
        recognize.setBackgroundColor(Color.parseColor("#8395a7"));
        LableSuccess = "ABSEN SEKARANG";
        LableError = "WAJAH TIDAK SESUAI";

        textTitle.setText("Verifikasi Wajah");
        textSubTitle.setText("Pastikan wajah kamu berada tepat didepan kamera");


    }


}

