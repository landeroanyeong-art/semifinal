#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Servo.h> // 1. Added Servo Library

#define SS_PIN 10
#define RST_PIN 9
#define SERVO_PIN 3 // 2. Define Servo Pin

LiquidCrystal_I2C lcd(0x27, 16, 2);
MFRC522 rfid(SS_PIN, RST_PIN);
Servo myServo; // 3. Create Servo Object

const unsigned long classInterval = 60000;

struct Student {
  String uid;
  String name;
  bool scanned;
  unsigned long lastScan;
};

Student students[] = {
  {"94 5F 20 6C", "Kirbie Alas", false, 0},
  {"D1 12 D1 06", "Angelo Cabal", false, 0},
  {"5C 13 08 2E", "Renz Cenangote", false, 0},
  {"BC 61 13 2E", "Semuel Rogador", false, 0},
  {"2A B8 CA B3", "Khean Landero", false, 0},
  {"95 52 22 07", "Grace Kadusale", false, 0},
  {"BC 34 01 29", "Mark Malasabas", false, 0}
};

int totalStudents = sizeof(students) / sizeof(students[0]);

void setup() {
  Serial.begin(9600);

  lcd.init();
  lcd.backlight();

  SPI.begin();
  rfid.PCD_Init();

  myServo.attach(SERVO_PIN); // 4. Attach Servo
  myServo.write(0); // Set initial position (Closed)

  lcd.setCursor(0, 0);
  lcd.print("Attendance Sys");
  lcd.setCursor(0, 1);
  lcd.print("Ready to Scan");
  delay(1000);
  lcd.clear();
}

void loop() {
  lcd.setCursor(3, 0);
  lcd.print("Scan ID");

  // Reset and Check for RFID cards
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  // Convert UID to String
  String ID = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] < 0x10) ID += "0";
    ID += String(rfid.uid.uidByte[i], HEX);
    if (i < rfid.uid.size - 1) ID += " ";
  }
  ID.toUpperCase();

  Serial.println(ID);

  bool found = false;
  for (int i = 0; i < totalStudents; i++) {
    if (ID == students[i].uid) {
      found = true;
      unsigned long currentTime = millis();

      lcd.clear();
      lcd.setCursor(0, 0);
      lcd.print(students[i].name.substring(0, 16));

      // ONLY MOVE SERVO IF MARKED PRESENT OR INTERVAL PASSED
      if (!students[i].scanned || (currentTime - students[i].lastScan >= classInterval)) {
        students[i].scanned = true;
        students[i].lastScan = currentTime;

        lcd.setCursor(4, 1);
        lcd.print("Present");

        // --- SERVO ACTION ---
        myServo.write(120); // Open Door (90 degrees approx)
        delay(5000);        // Keep door open for 5 seconds
        myServo.write(0);   // Close Door (0 degrees)
      } else {
        lcd.setCursor(0, 1);
        lcd.print("Already Scanned");
        delay(2000);
      }
      break; 
    }
  }

  if (!found) {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Unknown Card");
    lcd.setCursor(0, 1);
    lcd.print("Access Denied");
    delay(2000);
  }

  lcd.clear();
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}