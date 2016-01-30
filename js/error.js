function testIfVariableIsNumber(variable, name) {
    if (!isFinite(String(variable))) {
        //name is not a number
    }
}

function testIfVariableIsBoolean(variable, name){
    if(typeof(variable) === "boolean"){ 
        //name is not a boolean
    }
}

function testIfVariableIsString(variable, name){
    if(typeof(variable) === "string"){ 
        //name is not a string
    }
}