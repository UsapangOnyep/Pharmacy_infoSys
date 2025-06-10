@echo off
REM Set Chrome and Edge paths
set CHROME_PATH="C:\Program Files\Google\Chrome\Application\chrome.exe"
set EDGE_PATH="C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"

REM Set your app URL
set APP_URL=http://localhost:1500/

REM Check if Chrome exists
if exist %CHROME_PATH% (
    echo Launching in Chrome...
    start "" %CHROME_PATH% --incognito --start-fullscreen %APP_URL%
) else (
    echo Chrome not found. Launching in Microsoft Edge...
    start "" %EDGE_PATH% --inprivate --start-fullscreen %APP_URL%
)
