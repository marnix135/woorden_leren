<?php

ini_set('display_errors', '1');
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login.php");
}

include('../header.php');
?>
<div class="container" id="containers" tyle="display: none;">
    <div class="row">
        <div id="enter_word" class="col-xs-12 col-sm-8 col-sm-offset-2">
            <div class="row">
                <div class="col-lg-8" id="input_container">
                    <h1 id="word1"></h1>
                    <p id="translate_to" class="light"></p>
                    <span class="light" id="right_answer_label" style="display: none;">Correct translation: </span>
                    <h2 id="right_answer" style="display: none;"></h2>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Translation" id="translation_input">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="check_button">Check</button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="progress">
                <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%">
                    <span id="progress-label">70% Complete</span>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="/javascript/run.js"></script>
