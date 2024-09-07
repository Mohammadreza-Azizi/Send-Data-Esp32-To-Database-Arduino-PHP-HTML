#include <WiFi.h>
#include <HTTPClient.h>

#include <OneWire.h>
#include <DallasTemperature.h>
const int oneWireBus = 5;
OneWire oneWire(oneWireBus);
DallasTemperature sensors(&oneWire);

String URL = "http://**************.php";

const char* ssid = "****"; 
const char* password = "****"; 

float temperature = 0;

String LED_id = "1";                  //Just in case you control more than 1 LED          //Each time we press the push button    
String data_to_send = "check_LED_status=" + LED_id;             //Text data to send to the server
unsigned int Actual_Millis, Previous_Millis;
int refresh_time = 200;               //Refresh rate of connection to website (recommended more than 1s)


//Inputs/outputs                    //Connect push button on this pin
int LEDpin = 18;                          //Connect LED on this pin (add 150ohm resistor)

void setup() {
   delay(10);
  Serial.begin(115200);

    pinMode(LEDpin, OUTPUT);                   //Set pin 18 as OUTPUT
    WiFi.begin(ssid, password);             //Start wifi connection
  Serial.print("Connecting...");
  while (WiFi.status() != WL_CONNECTED) { //Check for the connection
    delay(500);
    Serial.print(".");
  }

  Serial.print("Connected, my IP: ");
  Serial.println(WiFi.localIP());
  Actual_Millis = millis();               //Save time for refresh loop
  Previous_Millis = Actual_Millis; 

  sensors.begin(); 
}
void loop() {  
  //We make the refresh loop using millis() so we don't have to sue delay();
  Actual_Millis = millis();
  if(Actual_Millis - Previous_Millis > refresh_time){
    Previous_Millis = Actual_Millis;  
    if(WiFi.status()== WL_CONNECTED){
      HTTPClient http1;     //Check WiFi connection status  
      HTTPClient http2;                                  //Create new client

http2.begin(URL);
http2.addHeader("Content-Type", "application/x-www-form-urlencoded");

  int response_code = http2.POST(data_to_send);                                //Send the POST. This will giveg us a response code
      
      //If the code is higher than 0, it means we received a response
      if(response_code > 0){
        Serial.println("HTTP code " + String(response_code));                     //Print return code
  
        if(response_code == 200){                                                 //If code is 200, we received a good response and we can read the echo data
          String response = http2.getString(); 
          String response_body = response.substring(0, 1);                          //Save the data comming from the website
          Serial.print("Server reply: ");                                         //Print data to the monitor for debug
          Serial.println(response);
     Serial.println("--------------------------------------------------");
          //If the received data is LED_is_off, we set LOW the LED pin
           if(response_body == "1"){
            digitalWrite(LEDpin, HIGH);
            //Serial.println("HELLO");
           }
          //If the received data is LED_is_on, we set HIGH the LED pin
           else if(response_body == "0"){
            digitalWrite(LEDpin, LOW);
            //Serial.println("BYE");
          }  

        }//End of response_code = 200
      }//END of response_code > 0
      
      else{
       Serial.print("Error sending POST, code: ");
       Serial.println(response_code);
      }





 sensors.requestTemperatures();
 float temperatureC = sensors.getTempCByIndex(0);
 delay(5000);
  temperature = temperatureC;
  //-----------------------------------------------------------
  // Check if any reads failed.
  if (isnan(temperature)) {
    Serial.println("Failed to read from DS18B20 sensor!");
    temperature = 0;
   
  }
  //-----------------------------------------------------------
  Serial.printf("Temperature: %d °C\n", temperature);
  String postData = "temperature=" + String(temperature);

      
  http1.begin(URL);
  http1.addHeader("Content-Type", "application/x-www-form-urlencoded");


  int httpCode = http1.POST(postData);
  Serial.print("URL : "); Serial.println(URL); 
    Serial.print("Data: "); Serial.println(postData);
    

  


      http1.end(); 
      http2.end(); //End the connection
    }//END of WIFI connected
    else{
      Serial.println("WIFI connection error");
    }
  }
}
