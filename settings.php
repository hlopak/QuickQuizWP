<?php

namespace QuickQuiz;


// Make sure we don't expose any info if called directly
if ( !function_exists( __NAMESPACE__ . '\buildPage' ) )
{
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly. #2';
  exit;
}





/**
*/
function getHtml( $args )
{
  $defaults = array(
    'version' => 'v.0.0.0',
  );
  $args = wp_parse_args( $args, $defaults );

  $version = $args[ 'version' ];
  // $url_to_css = plugins_url( "quickquiz/css" );
  // $url_to_js = plugins_url( "quickquiz/js" );

  $html =<<< EOT

    <div class="wrap">

      <h1>QuickQuiz Generator</h1><span>{$version}</span>

      <div class="recordslist-block-1 clearfix">
        <h4>List of QuickQuizes in database</h4>
        <div id="recordslist-1" class="recordslist-1">
        </div>
      </div>
      
      <div class="buttons-block-1 clearfix">
        <div class="one-record clearfix">
          <label for="question-1">Name:</label>
          <input name="alias-1" id="alias-1" type="text" value="" placeholder="Name of QuickQuiz" />
          <div class="clearfix">Name of QuickQuiz has to have only letters and numbers, without spaces
           and another special symbols.</div>
        </div>
      </div>


      <div id="report-1" class="report clearfix"></div>

      <div class="buttons-block-1 clearfix">
        <!--  input type="button" value="Generate result" id="btn-GenerateResult-1" class="button-1" /  -->
        <input type="button" value="Start new QuickQuiz" id="btn-NewQQ-1" class="button-1" />
        <input type="button" value="Save QuickQuiz" id="btn-SaveQQ-1" class="button-1" />
        <!-- input type="button" value="Load QuickQuiz" id="btn-LoadQQ-1" class="button-1" /  -->
        <!-- input type="button" value="Load list of QuickQuiz" id="btn-LoadQQList-1" class="button-1" /  -->
      </div>

      <div class="buttons-block-1 clearfix">
        <input type="button" value="Add new record" id="btn-AddRecord" class="button-1" />
      </div>
      <div id="blue-buttons-place" class="buttons-block-1 clearfix">
        <!--  input type="button" value="1" id="btn-SwRecord-1" class="button-2 blue-buttons active" title="Record #1" /  -->
      </div>


      <div class="content-block-2 clearfix">

        <div id="one-record" class="one-record clearfix">
          <label for="question-1">Question:</label>
          <textarea name="question-1" id="question-1" placeholder="Question"></textarea><br/>
          <label for="answer-1">Answer:</label>
          <textarea name="answer-1" id="answer-1" placeholder="Answer"></textarea>
        </div>

      </div>

      <div class="result">
        <label for="result-1">Result:</label>
        <textarea name="result-1" id="result-1"></textarea>
      </div>

  
      <div class="content-block-2 clearfix">
        <h3>How to use QuickQuiz Generator?</h3>
        <p>
          To insert QuickQuiz into article you have to put shortcode with name of QuickQuiz into article.
        </p>
        <p>
          1. Create QuickQuiz. <br />
          2. Save QuickQuiz. <br />
          3. Take name of QuickQuiz. <br />
          4. Open article and put shortcode of QuickQuiz inside article.
        </p>
        <h3>What is shortcode?</h3>
        <p style="color:#333;">
          <span style="color:#30b; font-weight: bold;">[quickquiz name="<span style="color:#b00;">NAME</span>"]</span> 
            — shortcode. Shortcode consists of square brackets, word "quickquiz" and name of QuickQuiz. <br />
          <span style="color:#b00;">NAME</span> — name of QuickQuiz.
        </p>
      </div>

    </div>
EOT;

  return $html;
}



?>