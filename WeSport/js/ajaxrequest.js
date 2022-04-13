
function AjaxRequest(config) {
    // Request URL
    this.url          = null ;
    // Request method
    this.method       = 'get' ;
    // Response mime type
    this.handleAs     = 'text' ;
    // Request parameters
    this.parameters   = {} ;
    // AJAX transport (xmlHttpRequest object)
    this.transport    = null ;
    // On success callback
    this.onSuccess    = function() {} ;
    // On error callback
    this.onError      = function() {} ;
    // Cancel request method
    this.cancel       = function() {
        if (this.transport != null) {
            this.onError   = function() {} ;
            this.onSuccess = function() {} ;
            this.transport.abort() ;
        }
    } ;

    // Check config values
    if (typeof config != "object") {
        throw 'Request parameter should be an object' ;
    }

    // Check request URL parameter
    if (!config.url) {
        throw 'Request URL needed' ;
    }
    this.url = config.url ;

    // Check request method parameter
    if (config.method) {
        if(typeof config.method === "string") {
            var method = config.method.toLowerCase() ;
            if (method === "get" || method === "post")
                this.method = method ;
            else
                throw "'" + config.method + "' method not supported" ;
        }
        else {
            throw "'method' parameter should be a string" ;
        }
    }

    // Check request parameters parameter
    if (config.parameters) {
        if (config.parameters instanceof Object) {
            this.parameters = config.parameters ;
        }
        else {
            throw "'parameters' parameter should be a object" ;
        }
    }

    var callbackFound = false ;
    // Check onSuccess callback parameter
    if (config.onSuccess) {
        if (config.onSuccess instanceof Function) {
            this.onSuccess = config.onSuccess ;
            callbackFound = true ;
        }
        else {
            throw "'onSuccess' parameter should be a function" ;
        }
    }

    // Check onError callback parameter
    if (config.onError) {
        if (config.onError instanceof Function) {
            this.onError = config.onError ;
            callbackFound = true ;
        }
        else {
            throw "'onError' parameter should be a function" ;
        }
    }

    // Check whether onSuccess or onError callback parameter is present
    if (!callbackFound) {
        throw "'onSuccess' or 'onError' parameter not found" ;
    }

    // Check response mime type parameter
    if (config.handleAs) {
        if (typeof config.handleAs === 'string') {
            var handleAs = config.handleAs.toLowerCase() ;
            if (['text', 'json', 'xml'].indexOf(handleAs) !== -1) {
                this.handleAs = handleAs ;
            }
            else {
                throw "handleAs format '" + config.handleAs + "' not supported" ;
            }
        }
        else {
            throw "handleAs parameter should be a string" ;
        }
    }

    // Build transport
    this.transport = GetXmlHttpObject() ;

    // Build response callback function
    // Closure for this
    var requestThis = this ;
    this.transport.onreadystatechange = function() {
        ////////////////////////////////////////////////////////////////////////////////
        // A ECRIRE
        // On complete (readyState == 4 or "complete")
        if (this.readyState === "complete" || this.readyState === 4) {
            // On success result (Response code == 200)
            if (this.status === 200) {
                // Check response expected mime type ('text', 'json' or 'xml')
                let result = null ;
                switch (requestThis.handleAs) {
                    case "text":
                        result = this.responseText;
                        break;

                    case "json":
                        result = JSON.parse(this.responseText);
                        break;

                    case "xml":
                        result = this.responseXML;
                        break;
                }

                // Launch onSuccess callback with result parameter
                requestThis.onSuccess(result);
            }
            else {
                // Response code != 200 => Launch onError with parameters status and response content
                requestThis.onError(this.status, this.responseText);
            }
        }
    }

    // Build parameters string
    let parameters = new Array();
    // Iterate on parameters
    for (let i in this.parameters) {
        ////////////////////////////////////////////////////////////////////////////////
        // A ECRIRE
        // Escape parameter value with encodeURIComponent()
        // Store 'parameter_name=escaped_parameter_value' in array 'parameters'
        let escaped_parameter_value = encodeURIComponent(this.parameters[i]);
        parameters.push(i + "=" + escaped_parameter_value);
    }
    // Join escaped parameters with '&'
    let parametersString = parameters.join('&') ;
    if (this.method === 'get') {
        // Request method is 'get'
        ////////////////////////////////////////////////////////////////////////////////
        // A ECRIRE
        // Open transport
        // Send request
        this.transport.open('get', this.url + "?" + parametersString);
        this.transport.send();
    }
    else {
        // Request method is 'post'
        ////////////////////////////////////////////////////////////////////////////////
        // A ECRIRE
        // Open transport
        // Set content type request header
        // Send request with parameters
        this.transport.open('post', this.url);
        this.transport.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        this.transport.send(parametersString);
    }

    // Get XmlHttpRequest object
    function GetXmlHttpObject() {
        // XmlHttpRequest object
        let xmlHttp = null ;

        try {
            // Firefox, Opera 8.0+, Safari, IE 7+
            xmlHttp = new XMLHttpRequest();
        } catch (e) {
            // Internet Explorer - old IE - prior to version 7
            try {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {
                    throw "XMLHTTPRequest object not supported" ;
                }
            }
        }
        return xmlHttp ;
    }
}
