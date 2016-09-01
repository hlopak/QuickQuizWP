/*
  JS
*/



(function( O ){


let Records = [];
let ResultHTML = "";
let CurrentRecord = 1;
let QQ_vars = O; 


/*
*/
function startWhenReady( cb )
{
  let num = Math.random();
  let name = 'Symbol_for_' + num;
  document[ name ] = Object.assign({},{onreadystatechange:document.onreadystatechange});
  document.onreadystatechange=function(){if(document.readyState==="interactive"){
    if ( document[ name ].onreadystatechange )
      document[ name ].onreadystatechange();
    cb( );
  }};
}



/*
*/
String.prototype.addClass = function addClass( class1, remove_it )
{
  let parent = this;
  let stroka = parent.toString();
  let classes = stroka.split( ' ' );
  let filtered = classes.filter(function(value) {
    return value !== "" && value !== class1;
  });
  let new_classes = "";
  for( let i in filtered )
  {
    let ei = filtered[ i ];
    new_classes += ei + " ";
  }
  if ( remove_it !== true )
    new_classes += class1;
  return new_classes;
}



/*
*/
function getById( id_str )
{
  return document.getElementById( id_str );
}



/*
*/
function getByClassName( classname_str )
{
  return document.getElementsByClassName( classname_str );
}



/*
*/
function addUIElement( parent, html_tag_name, html, cb )
{
  let node = document.createElement( html_tag_name );
  // console.log( " ~ node==", node, 'html==', html, 'parent==', parent,
  // 'node.outerHTML==', node.outerHTML, 'node.parentNode==', node.parentNode );
  // node.outerHTML = html;
  node.innerHTML = html;
  parent.appendChild( node );
  if ( cb )
    cb( node );
}



/*
*/
function addBlueButton( parent )
{
  let num = getLastBlueButtonId( parent );
  num++;
  let node1 = null;
  addUIElement( parent, "input", "", function( node ){
    node.id = "btn-SwRecord-" + num;
    node.value = "" + num;
    node.type = "button";
    node.className = "button-2 blue-buttons";
    node.title = "Record #" + num;
    node.addEventListener( "click", clickBlueButton, false );
    node1 = node;
  });
  return { num: num, node: node1 };
}



/*
*/
function showActiveBlueButton( node )
{
  let arr = [],
      buts = getByClassName( 'blue-buttons' );
  for( let i=0; i<buts.length; i++ ) arr.push( buts[ i ] );
  arr.map(function(el){
    el.className = el.className.addClass( 'active', true );
  });
  node.className = node.className.addClass( 'active' );
}



/*
*/
function getLastBlueButtonId( parent )
{
  parent = parent || document;
  let num = parent.getElementsByClassName( "blue-buttons" ).length || 0;
  return num;
}



/*
*/
function addEmptyRecord( )
{
  Records.push( { question: "", answer: "" } );
}



/*
*/
function addNewRecord( )
{
  // let parent = getById( 'btn-SwRecord-1' ).parentNode;
  let parent = getById( 'blue-buttons-place' );
  let new_button = addBlueButton( parent );  // <-- Add new BlueButton.
  addEmptyRecord( );                         // <-- Add new object of record in 'Records'.
  showActiveBlueButton( new_button.node );   // <-- Show active BlueButton (only view).
  selectRecord( new_button.num );            // <-- Switch to active BlueButton/Record. 
  return new_button;  // {num, node}
}



/*
*/
function selectRecord( selected_num )
{
  let question = getById( "question-1" );
  let answer = getById( "answer-1" );
  // 1. Copy data from INPUT-fields to Record (in CurrentRecord position).
  Records[ CurrentRecord-1 ] = Object.assign( {}, { 
    question: question.value, 
    answer: answer.value
  });
  // 2. Copy data from Records (from selected position) to INPUT-fields.
  question.value = Records[ selected_num-1 ].question;
  answer.value = Records[ selected_num-1 ].answer;
  // 3. Set Current position equals to selected position.
  CurrentRecord = selected_num;
}



/*
*/
function generateResult( )
{
  selectRecord( CurrentRecord );

  let html = '';
  // html += '<link rel="stylesheet" href="css/qq-main.css" />'
  // html += '<script src="js/qq-script.js" async="true"></script>';
  // html += '<ul class="my-quick-quiz">';
  html += '<ul>';
  for( let i=0; i<Records.length; i++ )
  {
    html += '<li>';
    html += '<span>' + Records[ i ].question;
    html += '</span>';
    html += '<span>' + Records[ i ].answer;
    html += '</span>';
    html += '</li>';
  }
  html += '</ul>';
  ResultHTML = html;
  getById( 'result-1' ).value = html;
}



/*
*/
function checkAlias( node )
{
  let str = node.value;
  let str2 = str.replace( new RegExp( '[^a-zA-Z0-9\-_]+', 'ig' ), '' );
  node.value = str2;
  return node.value;
}



/*
*/
function prepareClearForm( )
{
  // -- Remove all Blue Buttons...
  let blue_buttons_place = getById( 'blue-buttons-place' );

  // 1. Remove event handlers.
  for( let i=0; i<blue_buttons_place.children.length; i++ )
    blue_buttons_place.children[ i ].removeEventListener( "click", clickBlueButton, false );
  // 2. Remove elements.
  while( blue_buttons_place.children.length > 0 )
    blue_buttons_place.removeChild( blue_buttons_place.children[ 0 ] );
  // 3. Erase INPUT-fields.
  let question = getById( "question-1" );
  let answer = getById( "answer-1" );
  let alias = getById( "alias-1" );
  let result = getById( "result-1" );
  question.value = "";
  answer.value = "";
  alias.value = "";
  result.value = "";
}



/*
*/
function buildForm( alias_str, new2_records )
{
  let blue_buttons_place = getById( 'blue-buttons-place' );

  Records = [];
  prepareClearForm( );
  let first_button = null;

  for( let i=0; i<new2_records.length; i++ )
  {
    let new_button = addBlueButton( blue_buttons_place );
    if ( i === 0 ) 
      first_button = Object.assign( {}, new_button );
    addEmptyRecord( );
    Records[ i ].question = new2_records[ i ].question;
    Records[ i ].answer = new2_records[ i ].answer;
  }
  if ( new2_records.length < 1 )
    addNewRecord( );
  
  getById( "question-1" ).value = Records[ 0 ].question;
  getById( "answer-1" ).value = Records[ 0 ].answer;
  getById( "alias-1" ).value = alias_str;
  CurrentRecord = 1;
  selectRecord( 1 );
  showActiveBlueButton( first_button.node );
  generateResult( );
}



/*
*/
function buildUIListOfRecords( new_list )
{
  let buttons_delrec = getByClassName( 'buttons-delrec' );
  for( let i=0; i<buttons_delrec.length; i++ )
    buttons_delrec[ i ].removeEventListener( "click", clickDeleteQQ, false );
  let recordslist = getById( 'recordslist-1' );
  for( let i=0; i<recordslist.children.length; i++ )
    recordslist.children[ i ].removeEventListener( "click", clickRecordinlist, true );
  while( recordslist.children.length > 0 )
    recordslist.removeChild( recordslist.children[ 0 ] );
  for( let i=0; i<new_list.length; i++ )
  {
    (function( _num, _alias ){
      addUIElement( recordslist, 'div', '', function( node ){
        node.id = "btn-RecordInList-" + _num;
        node.innerHTML = "" + _alias;
        node.innerHTML = '<span>' + _alias + '</span>';
        node.title = "Load record '" + _alias + "'";
        node._alias = _alias;
        node.className = "";
        node.realButton = true;
        node.addEventListener( "click", clickRecordinlist, false );
  
        addUIElement( node, 'span', '', function( node ){
          node.id = "btn-DelRec" + _num;
          // node.innerHTML = '<img src="/wp-content/plugins/quickquiz/img/173-bin.png" />';
          node.innerHTML = '<img src="' + QQ_vars.this_plugin_url + '/img/173-bin.png" />';
          node.title = "Delete record '" + _alias + "'";
          node._alias = _alias;
          node.className = "buttons-delrec";
          node.addEventListener( "click", clickDeleteQQ, false );
        });

      });
    })( i, new_list[ i ] );
  }
}



/*
*/
function startNewQQ( )
{
  prepareClearForm( );
  addNewRecord( );
}



/*
*/
function setAllListeners( )
{
  getById( 'btn-AddRecord' ).addEventListener( "click", clickAddNewRecord, false );
  // getById( 'btn-SwRecord-1' ).addEventListener( "click", clickBlueButton, false );
  // getById( 'btn-GenerateResult-1' ).addEventListener( "click", clickGenerateResult, false );
  getById( 'btn-NewQQ-1' ).addEventListener( "click", clickNewQQ, false );
  getById( 'btn-SaveQQ-1' ).addEventListener( "click", clickSaveQQ, false );
  // getById( 'btn-LoadQQ-1' ).addEventListener( "click", clickLoadQQ, false );
  // getById( 'btn-LoadQQList-1' ).addEventListener( "click", clickLoadQQList, false );
  getById( 'alias-1' ).addEventListener( "keyup", keyupAlias, false );
  getById( 'question-1' ).addEventListener( "keyup", keyupGenerate, false );
  getById( 'answer-1' ).addEventListener( "keyup", keyupGenerate, false );

  startNewQQ( );
  loadQQList( );
}






// *********************************************************************************************
//
// ************[ Events ]***********************************************************************
//
// *********************************************************************************************



/*
*/
function clickRecordinlist( event )
{
  // event.preventDefault();
  event.stopPropagation();
  // console.log( " ~ clickRecordinlist( event ) : ", event );
  // if ( event.eventPhase !== 2 ) return true;
  let node = event.target;
  while ( node.realButton !== true && node.nodeName !== "BODY" )
  {
    if ( node.nodeName === "BODY" ) return;
    node = node.parentNode;
  }

  let alias = node._alias;
  getById( 'alias-1' ).value = alias;
  loadQQ();
}



/*
*/
function clickBlueButton( event )
{
  // event.preventDefault();
  // event.stopPropagation();
  let node = event.target;
  let id = node.id;
  showActiveBlueButton( node );
  let res = id.match( /btn-SwRecord-([0-9]+)/ );  // "btn-SwRecord-${num}"
  let selected_num = parseInt( res[1], 10 );
  selectRecord( selected_num );
}



/*
*/
function clickAddNewRecord( event )
{
  // event.preventDefault();
  // event.stopPropagation();
  addNewRecord();
}



/*
*/
// function clickGenerateResult( event )
// {
//   generateResult( );
// }



/*
*/
function clickNewQQ( event )
{
  // event.preventDefault();
  // event.stopPropagation();
  startNewQQ( );
}



/*
*/
function clickDeleteQQ( event )
{
  // event.preventDefault();
  event.stopPropagation();
  let node = event.target.parentNode;
  let id = node.id;
  let alias = node._alias;
  console.log( " ~ deleteQQ(), alias= ", alias );
  deleteQQ( alias );
}



/*
*/
function clickSaveQQ( event )
{
  // event.preventDefault();
  // event.stopPropagation();
  saveQQ( );
}



/*
*/
// function clickLoadQQ( event )
// {
//   loadQQ( );
// }



/*
*/
// function clickLoadQQList( event )
// {
//   loadQQList( );
// }



/*
*/
function keyupAlias( event )
{
  let node = event.target;
  clearTimeout( node.timeoutID );
  node.timeoutID = setTimeout(function() {
    checkAlias( node );
  }, 1000);
}



/*
*/
function keyupGenerate( event )
{
  let node = event.target;
  clearTimeout( node.timeoutID2 );
  node.timeoutID2 = setTimeout(function() {
    generateResult( );
  }, 3000);
}






// *********************************************************************************************
//
// ************[ Ajax Requests ]****************************************************************
//
// *********************************************************************************************



/*
*/
function execRequest( action, send_data, treat_success, treat_failure )
{
  let data = {
    action: action,
    security: '',
    _wpnonce: QQ_vars.csrf_protector
  };

  if ( send_data )
    data.something = JSON.stringify( send_data );

  jQuery.ajax({
    url: ajaxurl,
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function( response ){
      // console.log( " ~ AJAX:OK:response: ", response );
      if ( response.success === true )
      {
        jQuery( '#report-1' ).after( '<div class="updated myUpdated"><p>OK.</p></div>' );
        if ( treat_success )
          treat_success( response );
      }
      else
      {
        jQuery( '#report-1' ).after( '<div class="error myError"><p>Error on server!</p></div>' );
        if ( treat_failure )
          treat_failure( response, 1 );
      }
      setTimeout(function() {
        jQuery( '.myUpdated, .myError' ).remove();
      }, 5000);
    },
    error: function( error ){
      // console.error( " ~ AJAX:ERR:error: ", error );
      jQuery( '#report-1' ).after( '<div class="error myError"><p>Error in request!</p></div>' );
      setTimeout(function() {
        jQuery( '.myError' ).remove();
      }, 5000);
      if ( treat_failure )
        treat_failure( error, 2 );
    }
  });
}



/*
*/
function saveQQ( )
{
  generateResult( );

  execRequest( 'save_qqrecord', { 
    alias: checkAlias( getById( 'alias-1' ) ), 
    records: Records, 
    result: ResultHTML
  }, function( response ){
    loadQQList( );
  });
}



/*
*/
function loadQQ( )
{
  execRequest( 'load_qqrecord', {
    alias: checkAlias( getById( 'alias-1' ) )
  }, function( response ){
    buildForm( response.data.data.alias, response.data.data.json );
  });
}



/*
*/
function deleteQQ( alias )
{
  execRequest( 'delete_qqrecord', {
    alias: alias
  }, function( response ){
    loadQQList( );
  });
}



/*
*/
function loadQQList( )
{
  execRequest( 'load_qqrecord_list', null, function( response ){
    buildUIListOfRecords( response.data.data );
  });
}

















startWhenReady(function(){

  //...
  setAllListeners( );

});
})( QQ_vars );




