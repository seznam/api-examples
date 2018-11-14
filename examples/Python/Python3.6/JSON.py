from requests import post
import json

class JsonSklik:
                
        def login(self):
            response = post("https://api.sklik.cz/drak/json/client.loginByToken", json=("token"!!!))
            #response = post("https://api.sklik.cz/drak/json/client.login",json=("email", "heslo"))
            res = response.json()
            if res["status"] == 200:
                self.session = res["session"]
                return True
            else:
                return False
    
        """
        Volani metody
        @return |Boolen    

        Other examples and date types
        OBJECT - "group":{"status":["active","suspend"],"isDeleted":False}
        ARRAY - 'ids': [1297587,123932]
        STRING[] - "displayColumns":["id","name"]
            import base64
            square = open('300_300.jpg',"rb").read().encode('base64')
        BINARY - 'image': square

        """
        def request(self):
            if self.login():
                response = response = post('https://api.sklik.cz/drak/json/ads.createReport', json=[{"session":self.session},
                {"dateFrom":"2018-01-22",
                "dateTo":"2018-02-23"}
                ])
                return response.json()
            else:
                return False

example = JsonSklik()
print(example.request())