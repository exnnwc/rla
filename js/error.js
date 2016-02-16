function testIfVariableIsNumber(variable, name) {
    if (!isFinite(String(variable))) {
        return false;
        //name is not a number
    }
    return true;
}

function testIfVariableIsBoolean(variable, name){
    if(typeof(variable) === "boolean"){ 
        //name is not a boolean
        return true;
    }
    return false;
}

function testIfVariableIsString(variable, name){
    if(typeof(variable) === "string"){ 
        //name is not a string
        return true;
    }
    return false;
}
function testStringForMaxLength(variable, maxLength, name){
    if (!testIfVariableIsString(variable, name)){
        return false;
    }
    if (variable.length>maxLength){
        //name is too long
        return false;
    }
    return true;
}