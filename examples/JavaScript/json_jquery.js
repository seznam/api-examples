/**
 * Call API method
 * @param {String} method  - API method 
 * @param {Object} parameters - parametrs for API method
 * @param {String} callback - Callback method after success API call
 * @param {Mixed} params - variables which will be push to callback method
 */
function sklikApi(method, parameters, callback, params) {
  $.ajax({
    contentType: 'application/json',
    method: 'POST',
    dataType: "json",
    url: 'https://api.sklik.cz/drak/json/' + method,
    data: JSON.stringify(parameters)
  }).done(function (response) {
    if (response.session) {
      session = response.session;
      if (callback) {
        callback(response, params);
      }
    } else {
      return false;
    }
  }).fail(function (jqXHR, textStatus) {
    return false;
  });
}

/**
 * Login user by Token
 */
function loginByToken() {
  //############# TOKEN
  var token = 'token';
  var userId = [242911];
  sklikApi('client.loginByToken', token, createReport, { 'userIds': userId });
}

/**
 * Create method by API
 * @param {Object} parameters 
 */
function createReport(response, parameters) {
  var params =
    [{ 'session': session, 'userId': parameters.userId },
    {
      'campaign': { 'status': ['active'] },
      'group': { 'status': ['active'] },
      'dateFrom': '2018-01-08',
      'dateTo': '2018-02-08'
    },
    { 'statGranularity': 'monthly' }
    ];

  sklikApi(
    'keywords.createReport',
    params,
    printer,
    parameters
  );
}

/**
 * Print createReports response
 * @param {Object} response 
 * @param {Object} parameters 
 */
function printer(response, parameters) {
  console.log(response.totalCount);
}




