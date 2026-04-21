import serial
import serial.tools.list_ports
import requests
import time

def find_arduino_port():
    """Automatically finds the COM port where an Arduino is connected."""
    ports = list(serial.tools.list_ports.comports())
    for p in ports:
        # Most Arduinos show up with 'Arduino', 'USB Serial', or 'CH340' in the description
        if 'Arduino' in p.description or 'USB' in p.description or 'CH340' in p.description:
            print(f"Found Arduino on {p.device}")
            return p.device
    return None

# --- INITIALIZE CONNECTION ---
target_port = find_arduino_port()

if not target_port:
    # Fallback to COM8 if auto-detection fails, or let user know
    target_port = 'COM8' 
    print(f"⚠️ Could not auto-detect Arduino. Trying default {target_port}...")

try:
    arduino = serial.Serial(port=target_port, baudrate=9600, timeout=1)
    time.sleep(2) # Give the connection a moment to settle
    print(f"✅ Bridge Active on {target_port}: Waiting for Scans...")
except Exception as e:
    print(f"❌ ERROR: Could not connect to {target_port}.")
    print("Check if the Arduino is plugged in or if the Serial Monitor is open.")
    exit()

# --- MAIN LOOP ---
while True:
    try:
        if arduino.in_waiting > 0:
            raw_data = arduino.readline().decode('utf-8', errors='ignore').strip()
            
            if raw_data:
                # Remove all spaces and hidden characters
                clean_uid = "".join(raw_data.split())
                print(f"\n[SCAN] Raw: {raw_data} -> Clean: {clean_uid}")
                
                try:
                    # Update this URL if your folder name is different
                    url = f"http://localhost/attendance_system/rfid_handler.php?uid={clean_uid}"
                    response = requests.get(url)
                    
                    # Process server response
                    result = response.text.split('|')
                    
                    if result[0] == "SUCCESS":
                        print(f"✅ {result[1]}: {result[2]}")
                    elif result[0] == "NOT_FOUND":
                        print(f"❓ UNKNOWN CARD: {result[1]}")
                    elif result[0] == "LOCKED":
                        print(f"🔒 GATE LOCKED: {result[1]}")
                    else:
                        print(f"⚠️ SERVER: {response.text}")
                        
                except requests.exceptions.ConnectionError:
                    print("❌ SERVER ERROR: Make sure XAMPP (Apache) is running.")
                except Exception as e:
                    print(f"⚠️ Error sending to server: {e}")
                    
    except serial.SerialException:
        print("❌ Connection lost! Arduino was unplugged.")
        break
    except KeyboardInterrupt:
        print("\nStopping bridge...")
        break

if 'arduino' in locals():
    arduino.close()