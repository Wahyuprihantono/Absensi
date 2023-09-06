package com.absenkuy.Helper;

import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.RectF;

public class RectOverlay extends GraphicOverlay.Graphic {
    private int mColor = Color.BLUE;
    private float mStrokeWidth = 4.0f;
    private Paint mRectPaint;
    private GraphicOverlay graphicOverlay;
    private Rect rectF;

    public RectOverlay(GraphicOverlay graphicOverlay, Rect rectF) {
        super(graphicOverlay);
        mRectPaint = new Paint();
        mRectPaint.setColor(mColor);
        mRectPaint.setStyle(Paint.Style.STROKE);
        mRectPaint.setStrokeWidth(mStrokeWidth);

        this.graphicOverlay = graphicOverlay;
        this.rectF = rectF;
        postInvalidate();
    }

    @Override
    public void draw(Canvas canvas) {
        RectF rectf = new RectF(rectF);
        float cornerSize = Math.min(rectf.width(), rectf.height()) / 8.0f;

        rectf.left = translateX(rectf.left);
        rectf.top = rectf.top - 80;
        rectf.right = translateX(rectf.right);
        rectf.bottom = rectf.bottom - rectf.bottom / 8;
        canvas.drawRect(rectf, mRectPaint);
//        canvas.drawText("Yudi", translateX(rectf.left),rectf.top,mRectPaint);
    }
}
