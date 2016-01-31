function createRequirement(required_for, required_by, type) {
    console.log(required_for + " " + required_by + " " + type);
    if (required_for != required_by) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "create_requirement", required_for: required_for, required_by: required_by}
        })
                .done(function (result) {
                    console.log(result);
                    if (result.substr(0, 1) == "0") {
                        if (type == "for") {
                            $("#requirements_error" + required_for).html(result.substr(1, result.length));
                        } else if (type == "by") {
                            $("#requirements_error" + required_by).html(result.substr(1, result.length));
                        }
                    } else {
                        if (type == "for") {
                            listRequirements(required_for, type);
                        } else if (type == "by") {
                            listRequirements(required_by, type);
                        }
                    }
                });
    } else {
        if (type == "for") {
            $("#requirements_error" + required_for).html("Achievement cannot be a requirement of itself.");
        } else if (type == "by") {
            $("#requirements_error" + required_by).html("Achievement cannot be a requirement of itself.");
        }

    }
}





function deleteRequirement(id, achievement_id) {
    if (window.confirm("Are you sure you want to delete this as a requirement?")) {
        $.ajax({
            method: "POST",
            url: "/rla/php/ajax.php",
            data: {function_to_be_called: "delete_requirement", id: id}
        })
                .done(function (result) {
                    listRequirements(achievement_id, "for");
                    listRequirements(achievement_id, "by");
                });
    }
}













