@echo off
setlocal
echo ===================================================
echo   Penerbitan Sistem NAIMbif ke Google Cloud Run
echo ===================================================
echo.

set PATH=C:\Program Files (x86)\Google\Cloud SDK\google-cloud-sdk\bin;%PATH%

echo 1. Memeriksa log masuk Google Cloud...
call gcloud auth login

echo.
echo 2. Sila pastikan Project ID Google Cloud anda telah diset.
call gcloud projects list
echo.
set /p GCP_PROJECT="Masukkan Google Cloud Project ID anda: "

if not "%GCP_PROJECT%"=="" (
    call gcloud config set project %GCP_PROJECT%
)

echo.
echo 3. Mengaktifkan servis Cloud Run dan Cloud Build...
call gcloud services enable run.googleapis.com cloudbuild.googleapis.com

echo.
echo 4. Mula menerbitkan (deploy) ke Google Cloud Run (Rantau: Singapore / asia-southeast1)...
call gcloud run deploy naimbif --source . --region asia-southeast1 --allow-unauthenticated

echo.
echo ===================================================
echo   Selesai! Sistem anda kini aktif di Google Cloud.
echo ===================================================
pause
