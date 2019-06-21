/**
* Vychozi metoda, ktera spusti vsechny testy.
*/
function main() {
    credit();
    campaignKeeper();
}


/**
* Tato metoda sleduje pocet kliku za jeden den pro seznam kampani (array at line 16). 
* Pokud je hodnota nižší než stanovený prah, posle informacni email (int at line 14).
* Metoda bere vzdy pouze vcerejsi den!!! 
*/
function campaignKeeper() {
    const TOKEN = ''!!TOKEN;
    const CLICK_THRESHOLD = 50; //Minimalni hodnota kliku ktere kampane museji splnovat
    const EMAIL = ''!!EMAIL; //Email kam se posle informacni email
    const CAMPAIGN_IDS = [12345, 12345]; //Pole ID kampani, ktere se budou jednotlive proverovat
    const USER_ID = 12345; //ID uzivatele - lze zjistit z tokenu pomoci tohoto scriptu - https://bit.ly/2uMuKgL

    //Prihlaseni uzivatele
    var response = apiCall([TOKEN], 'client.loginByToken', 0);
    var session = response.session; //Ziskana session se ulozi do promenne

    //Nastaveni datumu na vcerejsi den -> uprava formatu ktery ocekava API
    var ys = new Date(Date.now() - 864e5);
    var yesterday = ys.toISOString(ys, "GTM - 1", 'yyyy-MM-dd');

    //Volani crateReport - vytvoreni prehledu vsech zvolenych kapani
    var restrictionFilter = { 'ids': CAMPAIGN_IDS, 'dateFrom': yesterday, 'dateTo': yesterday };
    var response = apiCall([{ 'session': session, 'userId': USER_ID }, restrictionFilter], 'campaigns.createReport', 0);

    //Nacteni statistiky pro veschny kampane
    var displayOptions = { 'offset': 0, 'limit': CAMPAIGN_IDS.length, 'displayColumns': ['id', 'name', 'clicks'], 'allowEmptyStatistics': true };
    var response = apiCall([{ 'session': session, 'userId': USER_ID }, response.reportId, displayOptions], 'campaigns.readReport', 0);

    //Postupna iterace nad statistikami, ty co neprojdou se zaznamenaji do zpravy.
    var message = '';
    var sendEmail = false;
    for (var i = 0; i < response.report.length; i++) {
        var record = response.report[i];
        if (record.stats[0].clicks < CLICK_THRESHOLD) {
            message = message + "Pro účet ... a kampaň " + record.name + " s ID [" + record.id + "] je počet prokliků za den " + yesterday + " pouze " + record.stats[0].clicks + "\n\r";
            sendEmail = true;
        }
    }

    //Pokud se nasla kampan, ktera nesplnuje podminku, pak se posle email
    if (sendEmail) {
        MailApp.sendEmail(EMAIL,
            "API SCRIPT - low campaign clicks",
            message);
    }
}


/**
* Metoda kontroluje zda dany ucet ma dostatek budgetu na uctu
* Token MUSI byt token uctu, nikoliv jeho spravcovsky
*/
function credit() {

    const TOKEN = ''!!TOKEN;
    const THRESHOLD = 1000 * 100; // 1000 Kc -> prevedeno na halere
    const EMAIL = ''!!EMAIL;

    //Prihlaseni uzivatele
    var response = apiCall([TOKEN], 'client.loginByToken', 0);
    var session = response.session; //Ziskana session se ulozi do promenne

    //Nacteni informaci o uzivateli
    var response = apiCall([{ 'session': session }], 'client.get', 0);

    //Ziskani zbyvajicicho kreditu
    var creditRemain = response.user.walletCreditWithVat;

    //Pokud kredit klesl pod urceny prah, pak infomovat na email
    if (creditRemain < THRESHOLD) {
        MailApp.sendEmail(EMAIL,
            "API SCRIPT - low credit",
            "Pro účet ... zůstává pouze " + Math.floor(creditRemain / 100) + " Kč");
    }

}

/**
* Metoda obsluhuje provolavani API
* @param {Object} parameters - telo volani (parametry dane metody)
* @param {string} method - metoda ktera se ma volat
* @param {int} retry - pocet pokusu provolani (pokud volani skonci chybou, rekurentne se metoda zanori a vola znova)
*/
function apiCall(parameters, method, retry) {
    //Jenom abych nezasypal API dotazy, proto pred kazdym volani to na chvili uspim
    Utilities.sleep(200);
    try {
        //Volani API
        var stat = UrlFetchApp.fetch('https://api.sklik.cz/drak/json/' + method, {
            'method': 'post',
            'contentType': 'application/json',
            'muteHttpExceptions': true,
            'payload': JSON.stringify(parameters)
        });
        //Odpoved se extrahuje z JSON formatu
        var response = JSON.parse(stat);
        //Kontrola existence validni odpovedi
        if (response == undefined || response.status == undefined) {
            throw 'Have no status message';
        }
        if (stat && stat.getResponseCode() == 200) {
            return response;
        } else {
            throw 'Have no positive HTTP status';
        }
    } catch (e) {
        if (retry < 5) {
            Utilities.sleep(2000);
            return apiCall(parameters, method, ++retry);
        } else {
            return false;
        }
    }
} 