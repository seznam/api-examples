from requests import post
import json

SSP_API_URL = "https://api.sklik.cz/ssp/json/"


class JsonSklik:      

    """
    Prihlasovani
    @param Boolean token - rozhoduje jestli probehne prihlaseni pomoci tokenu 
    """
    def login(self, token = True):
        if token:
            response = post(SSP_API_URL + "/login/token", json=({"token":!!!""}))
        else:
            response = post(SSP_API_URL + "/login",json=({"username": "email", !!!"password": !!!"heslo"}))
        res = response.json()
        if res["status"] == 200:
            self.session = res["session"]
            return True
        else:
            return False

    """
    Volani metody
    @return mixed|Boolen
    """
    def request(self):
        if self.login():
            params = {
                'session': self.session,                
                'dateFrom': '2019-01-01', 
                'dateTo': '2019-02-01',
                #'allowEmptyStatistics': True,
                #'granularity': 'yearly',
                #'byDevice': False,
                #'bySource': False,
                #'webIds': []     
            }
            response = post(SSP_API_URL + '/webs/stats', json=params)
            return response.json()
        else:
            return False

example = JsonSklik()
print(example.request())