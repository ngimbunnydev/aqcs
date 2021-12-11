/**************************
 *
 * by: phearun
 * Local Storage
 * date: 19/01/2021
 *
 **************************/

function addNewItem(objectName = 'Products', data = {}) {
    var object_data = getObject({ objectName: objectName });

    if (object_data != "" && object_data != null) {
        object_data.unshift(data);
    } else {
        object_data = new Array();
        object_data[0] = data;
    }

    // Convert object into JSON string and save it into storage
    localStorage.setItem(objectName, JSON.stringify(object_data));
}

/*** Get check existing item  ***/
function isHasItem(id, objectName = 'Products') {
    if (objectName === "" && id) {
        return false;
    }
    let data = getObject({ objectName: objectName });
    if (data == "" || data == null) {
        return false;
    }
    for (var k in data) {
        if (data[k].id == id) {
            return true;
        }
    }
    return false;
}

/*** Get index number by ID  ***/
function getItemIndexOf(id, objectName = 'Products') {
    if (isHasItem(id, objectName) === false) {
        return -1;
    }
    let data = getObject({ objectName: objectName });
    if (data === undefined || data.length == 0) {
        return -1;
    }
    for (var k in data) {
        if (data[k].id == id) {
            return k;
        }
    }
    return -1;
}

/*** Get Object From Local Storage  ***/
function getObject(options = {}) {
    // init default options
    var default_options = {
        objectName: 'Products'
    };

    // Merge default options with options
    $.extend(default_options, options);
    // get item with object name
    var data_string = localStorage.getItem(default_options.objectName);
    var object_data = JSON.parse(data_string);
    if (object_data == "" && object_data == null) {
        object_data = new Array();
    }
    return object_data;
}

/*** Set Object to Local Storage  ***/
function setObject(data, objectName = 'Products') {
    localStorage.setItem(objectName, JSON.stringify(data));
}

/*** count items  ***/
function getCountItem(objectName = 'Products') {
    let data = getObject({ objectName: objectName });
    if (data == "" || data == null) {
        return 0;
    }
    return data.length;
}

/*** Remove item by Index  ***/
function getRemoveObjectItem(objectName = "Products", options = {}) {
    // init default options
    var default_options = {
        index: 0, // index array to remove
        item: 1, // many items to remove
    };

    // Merge default options with options
    $.extend(default_options, options);

    var object_data = getObject({ objectName: objectName });
    var is_removed = false;
    if (object_data != "" && object_data != null) {
        object_data.splice(default_options.index, default_options.item);
        localStorage.setItem(objectName, JSON.stringify(object_data));
        is_removed = true;
    }
    return is_removed;
}

/*** Update item by Index  ***/
function getUpdateObjectItem(indexOf, data = {}, objectName = 'Products') {
    if (indexOf < 0) {
        return false;
    }
    var object_data = getObject({ objectName: objectName });
    if (object_data != "" && object_data != null) {
        if (object_data[indexOf]) {
            let current_data = object_data[indexOf];
            let params = Object.assign(current_data, data);
            object_data[indexOf] = params;
            localStorage.setItem(objectName, JSON.stringify(object_data));
            return true;
        }
        return false;
    }
    return false;
}

// init count item rows
function initItemListCount(containerId = '#countListItem', objectName = 'Products') {
    $(containerId).text(getCountItem(objectName));
}

/*
 * get value from url by paramater name
 */
function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        } else { return ''; }
    }
}