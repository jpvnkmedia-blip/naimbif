# Skrip Penerbitan Pantas ke Google Cloud Run (PowerShell)
$env:PATH = "C:\Program Files (x86)\Google\Cloud SDK\google-cloud-sdk\bin;$env:PATH"

Write-Host "===================================================" -ForegroundColor Cyan
Write-Host "  Penerbitan Sistem NAIMbif ke Google Cloud Run" -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Log Masuk
Write-Host "1. Memulakan log masuk Google Cloud..." -ForegroundColor Yellow
gcloud auth login

# 2. Senarai Projek
Write-Host "`n2. Senarai Projek Google Cloud anda:" -ForegroundColor Yellow
gcloud projects list

$projectId = Read-Host "`nMasukkan Google Cloud Project ID anda"
if ($projectId) {
    gcloud config set project $projectId
}

# 3. Aktifkan API yang diperlukan
Write-Host "`n3. Mengaktifkan servis Cloud Run dan Cloud Build..." -ForegroundColor Yellow
gcloud services enable run.googleapis.com cloudbuild.googleapis.com

# 4. Deploy ke Cloud Run
Write-Host "`n4. Mula menerbitkan (deploy) ke Google Cloud Run (Rantau: Singapore / asia-southeast1)..." -ForegroundColor Yellow
gcloud run deploy naimbif --source . --region asia-southeast1 --allow-unauthenticated

Write-Host "`n===================================================" -ForegroundColor Cyan
Write-Host "  Penerbitan Berjaya! Sistem anda kini aktif di Google Cloud." -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Cyan
