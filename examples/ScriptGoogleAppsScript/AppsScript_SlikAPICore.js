/**
* Pripojeni knihovny APICore 
* Library ID - 1M7AAXYbliz_oI0kgHG-rOG3IQw2yqKimgCJ9kPZwzARAwV3vkeq3oCJE
* viz: https://developers.google.com/apps-script/guides/libraries
*/

/**
* Je treba vytvorit CALC soubor s nazvem gSetting!!!
*/
function mainApiCore() {

    //ID slozky kde budou ulozeny vsechny soubory
    const FOLDER_ID = ''!!FOLDER_ID;

    //Nacteni a propojeni jednotlivych komponent
    this.rFolder = new SklikAPI.ApiFolder(FOLDER_ID); //Sprava slozky se soubory
    this.rSetup = new SklikAPI.FileOfSetup(FOLDER_ID); //Nacteni konfiguracniho souboru
    this.rEmail = new SklikAPI.ReportEmail(this.rSetup); //Posilani emailu s reporty
    this.rLogger = new SklikAPI.ProblemLogger(this.rFolder, this.rSetup, this.rEmail); //Vytvoreni logovaciho souboru mapujici stav procesu
    this.rApi = new SklikAPI.APIConnection(this.rSetup, this.rLogger); //Komunikace s SklikAPI

    //Aktivace komponent - nacteni potrebnych informaci
    this.rSetup.load();
    this.rLogger.setup();
    this.rEmail.setup();

    //Ulozeni nastaveni do gSetting - jednorazova vec (lze udelat i rucne v souboru). 
    var options = {
        defaultVal: {
            desc: 'popis bloku',
            reportEmail: {
                name: 'reportEmail',
                value: 'email',
                type: 'string',
                desc: 'popis informace'
            },
            token: {
                name: 'token',
                value: ''!!Token,
                type: 'string',
                desc: 'popis informace'
            }
        }
    };
    this.rSetup.setup(options);

    //Zahajeni testu
    this.rLogger.newTest('Load new test'); //Vytvoreni logovaci instance - zaznamu
    this.rApi.sklikApiLogin(); //Prihlaseni uzivatele

    //Provolani API - potrebne tyto informace - parameters, method, cbClass, cbMethod, param
    //Kde cbClass a cbMethod jsou call back funkce a param jsou hodnoty, ktere se maji predat dale
    this.rApi.sklikApi([{ 'session': this.rApi.getSession() }],
        'client.get',
        this,
        'getReponse',
        {}
    );
}

//Callback funkce pro volalni api na 54
function getReponse(response, param) {
    Logger.log(response);
    Logger.log(param);
}
