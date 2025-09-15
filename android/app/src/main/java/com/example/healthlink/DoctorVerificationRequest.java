package com.example.healthlink;

import android.util.Log;

import com.android.volley.AuthFailureError;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.HttpHeaderParser;

import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.util.Map;
import java.util.HashMap;

public class DoctorVerificationRequest extends Request<NetworkResponse> {
    private final Response.Listener<NetworkResponse> mListener;
    private final Response.ErrorListener mErrorListener;
    private final Map<String, String> mParams;
    private final Map<String, DataPart> mByteData;
    private final String mBoundary = "doctor_verification_" + System.currentTimeMillis();

    public DoctorVerificationRequest(int method, String url,
                                     Response.Listener<NetworkResponse> listener,
                                     Response.ErrorListener errorListener,
                                     Map<String, String> params,
                                     Map<String, DataPart> byteData) {
        super(method, url, errorListener);
        this.mListener = listener;
        this.mErrorListener = errorListener;
        this.mParams = params;
        this.mByteData = byteData;
    }

    @Override
    public Map<String, String> getHeaders() throws AuthFailureError {
        Map<String, String> headers = new HashMap<>();
        headers.put("Accept", "application/json");
        return headers;
    }

    @Override
    protected Map<String, String> getParams() throws AuthFailureError {
        return mParams;
    }

    @Override
    protected Response<NetworkResponse> parseNetworkResponse(NetworkResponse response) {
        try {
            return Response.success(
                    response,
                    HttpHeaderParser.parseCacheHeaders(response)
            );
        } catch (Exception e) {
            return Response.error(new VolleyError(e));
        }
    }

    @Override
    protected void deliverResponse(NetworkResponse response) {
        mListener.onResponse(response);
    }

    @Override
    public void deliverError(VolleyError error) {
        mErrorListener.onErrorResponse(error);
    }

    @Override
    public byte[] getBody() throws AuthFailureError {
        ByteArrayOutputStream bos = new ByteArrayOutputStream();
        DataOutputStream dos = new DataOutputStream(bos);
        try {
            // Build multipart form data
            String lineEnd = "\r\n";
            String twoHyphens = "--";

            // Add text parameters
            if (mParams != null && mParams.size() > 0) {
                for (Map.Entry<String, String> entry : mParams.entrySet()) {
                    dos.writeBytes(twoHyphens + mBoundary + lineEnd);
                    dos.writeBytes("Content-Disposition: form-data; name=\"" + entry.getKey() + "\"" + lineEnd);
                    dos.writeBytes("Content-Type: text/plain; charset=UTF-8" + lineEnd);
                    dos.writeBytes(lineEnd);
                    dos.writeBytes(entry.getValue());
                    dos.writeBytes(lineEnd);
                }
            }

            // Add file data
            if (mByteData != null && mByteData.size() > 0) {
                for (Map.Entry<String, DataPart> entry : mByteData.entrySet()) {
                    DataPart dataPart = entry.getValue();
                    dos.writeBytes(twoHyphens + mBoundary + lineEnd);
                    dos.writeBytes("Content-Disposition: form-data; name=\"" + entry.getKey() + "\"; filename=\"" + dataPart.getFileName() + "\"" + lineEnd);
                    dos.writeBytes("Content-Type: " + dataPart.getType() + lineEnd);
                    dos.writeBytes("Content-Transfer-Encoding: binary" + lineEnd);
                    dos.writeBytes(lineEnd);
                    dos.write(dataPart.getContent());
                    dos.writeBytes(lineEnd);
                }
            }

            // End of multipart form data
            dos.writeBytes(twoHyphens + mBoundary + twoHyphens + lineEnd);
            return bos.toByteArray();
        } catch (IOException e) {
            Log.e("DoctorVerification", "Error creating multipart request", e);
            throw new AuthFailureError("Error creating multipart request", e);
        }
    }

    @Override
    public String getBodyContentType() {
        return "multipart/form-data; boundary=" + mBoundary;
    }

    public static class DataPart {
        private String fileName;
        private byte[] content;
        private String type;

        public DataPart(String name, byte[] data, String mimeType) {
            fileName = name;
            content = data;
            type = mimeType;
        }

        public String getFileName() {
            return fileName;
        }

        public byte[] getContent() {
            return content;
        }

        public String getType() {
            return type;
        }
    }
}