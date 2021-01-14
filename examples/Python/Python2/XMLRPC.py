import xmlrpclib
from datetime import datetime
import time

class XMLRPCSklik:
        
        def __init__(self):
            self.p = 'https://api.sklik.cz/drak/RPC2'
        
        def login(self):
            self.p = xmlrpclib.ServerProxy('https://api.sklik.cz/drak/RPC2')
            result = self.p.client.loginByToken("token"!!!)
            if result["status"] == 200:
                self.session = result["session"]
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
        BINARY - 'image': xmlrpclib.Binary(square)

        """
        def request(self):
            if self.login():
                response = self.p.campaigns.createReport({'session':self.session},
                    { "dateFrom" : datetime.strptime("1.1.2015", "%d.%m.%Y"),
                      "dateTo" : datetime.strptime("31.12.2017", "%d.%m.%Y")})
                return response
            else:
                return False

asa = XMLRPCSklik()
print asa.request()