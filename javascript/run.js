var ltr = false;
var rtl = false;
var mixed = false;
var study = false;
var test = false;

var list_data;

var current_word = "";
var current_translation = "";
var current_index = 0;
var current_pair;

var finished = false;
var words = [];
var wrong = [];
var correct = [];

var answered = false;

function init() {
    var id = urlParam("id");

    if (id == null) {
        console.log("not found");
    } else if (Math.floor(id) != id && !$.isNumeric(id)) {
        console.log("not found 2");
    } else {
        var url = "/list/json.php?id=" + id;

        $.getJSON(url, function(data) {
            switch (urlParam("a")) {
                case "study":
                    study = true;
                    break;
                case "test":
                    test = true;
                    break;
                default:
                    study = true;
                    break;
            }

            switch (urlParam("b")) {
                case "ltr":
                    ltr = true;
                    $("#translate_to").text("Translate to " + data.language_2);
                    break;
                case "rtl":
                    rtl = true;
                    $("#translate_to").text("Translate to " + data.language_1);
                    break;
                case "mixed":
                    mixed = true;
                default:
                    ltr;
                    break;
            }

            // Shuffle the aray
            words = shuffle(data.words);
            $("#container").show();

            list_data = data;

            run();
        });
    }
}

function run() {
    // Get first element every time of the shuffled aray
    current_pair = words[0];

    if (ltr) {
        current_word = current_pair.word1;
        current_translation = current_pair.word2;
    } else if (rtl) {
        current_word = current_pair.word2;
        current_translation = current_pair.word1;
    } else if (mixed) {
        var random_boolean = Math.random() >= 0.5;

        if (random_boolean) {
            current_word = current_pair.word1;
            current_translation = current_pair.word2;
            $("#translate_to").text("Translate to " + data.language_2);
        } else {
            current_word = current_pair.word2;
            current_translation = current_pair.word1;
            $("#translate_to").text("Translate to " + data.language_1);
        }
    }

    $("#word1").text(current_word);
}

$("#check_button").click(function() {
    if (answered) {
        neutralise();
        answered = false;
        run();
    } else {
        answered = true;

        var entered = $("#translation_input").val();

        // Remove first element from words
        words.shift();

        if (current_translation === entered) {
            // Push current word pair to the 'correct' array
            correct.push(current_pair);

            display_right();
        } else {
            // Push current word pair to the 'wrong' array
            wrong.push(current_pair);

            // Ask current word again after 2 tries
            if (study) {
                if (words.length >= 2) {
                    words.splice(2, 0, current_pair);
                } else {
                    words.push(current_pair);
                }
            }

            display_wrong();
        }
    }
});

function display_right() {
    $("#word1").css("color", "#5cb85c");
    $("#translation_input").css("border-color", "#5cb85c");
    $("#check_button").removeClass("btn-danger");
    $("#check_button").addClass("btn-success");
    $("#check_button").text("Next");
}

function display_wrong() {
    $("#word1").css("color", "#d9534f");
    $("#translation_input").css("border-color", "#d9534f");
    $("#check_button").removeClass("btn-success");
    $("#check_button").addClass("btn-danger");
    $("#right_answer").text(current_translation);
    $("#right_answer").show();
    $("#right_answer_label").show();
    $("#check_button").text("Next");
}

function neutralise() {
    $("#word1").css("color", "initial");
    $("#translation_input").css("border-color", "#ccc");
    $("#check_button").removeClass("btn-success");
    $("#check_button").removeClass("btn-danger");
    $("#check_button").text("Check");
    $("#right_answer").hide();
    $("#right_answer_label").hide();
}

function urlParam(name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    } else {
        return decodeURI(results[1]) || 0;
    }
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function shuffle(arr) {
    for (var j, x, i = arr.length; i; j = parseInt(Math.random() * i), x = arr[--i], arr[i] = arr[j], arr[j] = x);
    return arr;
}

$(document).ready(function() {
    init();
});
