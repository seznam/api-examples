#include <iostream>

#include <cpr/cpr.h>
#include <nlohmann/json.hpp>

using nlohmann::json;

class JsonApi {
private:
	std::string address;
	std::string session;
	std::string report_id;

    nlohmann::json call(const std::string &method, json &data) {
    	auto response = cpr::Post(cpr::Url{address + method}, cpr::Body{data.dump()}, cpr::Header{{"Content-Type", "application/json"}});
    	auto json = nlohmann::json::parse(response.text);
    	std::cout <<json.dump()<<std::endl;
    	try {
			session = json["session"];
    	} catch (...) {}
    	return json;
    }

public:
	JsonApi(std::string address): address(address) {}
	bool login(std::string token) {
		json j = {token};
		auto resp = call("/client.login", j);
		return resp["status"] == 200;
	}

	nlohmann::json create_report (std::string date_from, std::string date_to) {
		json _json = json::parse("[{\"session\": \"" + session + "\"},"+
            "{\"dateFrom\":\"" + date_from + "\" ," +
            "\"dateTo\":\"" + date_to +"\"}" +
            "]");
		auto response = call("/ads.createReport", _json);
		std::cout << response;
		if (response["status"] == 200) {
			report_id = response["reportId"];
		}
		return response;
	}
};

int main() {
	JsonApi test("https://api.sklik.cz/jsonApi/drak");
	if (test.login("token")) {
		auto resp = test.create_report("2018-07-01", "2018-08-01");
	}


}
