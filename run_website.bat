start iexplore http://localhost/prwb_1920_C09/setup/install
set Delay=3
set killer=%temp%\kill.bat
echo > "%killer%" ping localhost -n %Delay% ^> nul
echo>> "%killer%" tasklist ^| find /i "iexplore.exe" ^> nul ^&^& taskkill /f /im iexplore.exe
start /b "Timeout" "%killer%"
timeout 3
start chrome http://localhost/prwb_1920_C09/
taskkill /F /IM cmd.exe