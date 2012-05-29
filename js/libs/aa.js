/**
 * client for call app manager soap server
 *
 * Example:
 *
  var client = Client;
  client.init({
    aa_app_id:{APP_MODEL_ID},
    aa_app_secret:'{APP_MODEL_SECRET}',
    aa_inst_id:{APP_INST_ID},
    fb_page_id:false
  });

  var ret=client.call("getInstanceId");
  //alert(ret);

  //var ret=client.getInstance();
  alert(ret.length)
  for(var i=0; i< ret.length; i++)
  {
     alert(ret[i].key);
     alert(ret[i].value);
  }

  var ret=client.getConfig("fb_color,bg_color");
  //alert(ret);
  //displayObject(ret);


  var ret=client.getConfigByType("text");
  alert(ret.length);
  for(var i=0; i< ret.length; i++)
  {
     alert(ret[i].key);
     alert(ret[i].value);
  }

  var ret=client.getConfigById('fg_color');
  alert(ret);


*/


var  Client=new Object();

Client.url="https://www.app-arena.com/manager/server/soap3.php";
//Client.url="http://www.app-arena.com/manager/server/soap3.php";

Client.setServerUrl=function(server_url){
  this.url=server_url;
};

Client.soap_params={
  aa_app_id:false,
  aa_app_secret:false,
  aa_inst_id:false,
  fb_page_id:false
};



Client.error_msg='';

/*
Client.setSoapParams=function(params){
  this.soap_params=params;
};
*/


//=============== methods ========================
Client.init=function(params){
  this.soap_params=params;
};

/**
* the method will call server's method
* every other method will use this to retrieve data
*/
Client.call=function(method,params){
  try
  {
    var pl = new SOAPClientParameters();

    //the params order  is the sever's params order
    //but params key do not have any meaning

    pl.add("method",method);

    pl.add("soap_params",this.soap_params);

    //if(typeof(params) != 'object')
     // params={};

    pl.add("params",params);

    //displayObject(params);
    //do real call
    var result = SOAPClient.invoke(this.url, "call", pl, false);
    //alert(result);
    //displayObject(result);

    if( result != false)
    {
      return result;
    }
    else
    {
      this.error_msg="call method $method return false";
      return false;
    }
  }
  catch(e)
  {
    this.error_msg=e;
    return false;
  }

};

/**
* if call some method failed, can call this to get the error msg
*/
Client.getErrorMsg=function()
{
  return this.error_msg;
};

/**
* get app's current aa_inst_id, if you only known the fb_page_id
*
* @return int
*/
Client.getInstanceId=function()
{
  var instid = this.call('getInstanceId');
  return instid;
};

/**
* get all data  ,(instance and config)
*
* @return array
*/
/*
Client.getData=function()
{
  result = this.call('getData');
  return result;
};
*/

/**
* only get instance data
*
* @return array
*/
Client.getInstance=function()
{
  var result = this.call('getInstance');

  //the identifier not exist
  if(exists(result) == false)
  {
    return false;
  }
  /*
  else if(exists(result.item))
  {
    // this is for only one item ,
    //result format:
    // {
    //  IDENTIFIER{
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    //  }
    // }
    //
    var data=new Object()
    result=result.item.value;
    //displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
      var key=result[i].key;
      var value=result[i].value;

      data[key]=value;
    }

    data['id']=data['identifier'];

    return data;
  
  }
  */
  else
  {
    // this is for more then one item ,
    // there are different from one item and multi items, 
    // seems js soap client's parse problem
    //
    //displayObject(result);
    //handle the result
    //this js soap client seems strange ..
    //so handle the result to easy use format
    //result format:
    // {
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    // }
    //
    var data=new Object()
    //result=result.item.value;
    //displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
        var key=result[i].key;
        var value=result[i].value;

        data[key]=value;
    }

    return data;
  }

};

/**
* only get config data
*
* @params Mix identifiers , if false , mean get all config data, if is config identifiers array, only get the value of these identifiers
*
* @return array
*/
Client.getConfig=function(identifiers)
{
  var result = this.call('getConfig',identifiers);
  //the identifier not exist
  if(exists(result) == false)
  {
    return false;
  }
  else if(exists(result.item))
  {
    // this is for only one item ,
    //result format:
    // {
    //  IDENTIFIER{
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    //  }
    // }
    //
    var data=new Object()
    result=result.item.value;
    //displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
      var key=result[i].key;
      var value=result[i].value;

      data[key]=value;
    }

    data['id']=data['identifier'];

    return data;
  
  }
  else
  {
    // this is for more then one item ,
    // there are different from one item and multi items, 
    // seems js soap client's parse problem
    //
    //displayObject(result);
    //handle the result
    //this js soap client seems strange ..
    //so handle the result to easy use format
    //result format:
    // {
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    // }
    //
    var data=new Object()
    //result=result.item.value;
    displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
      var item=result[i].value;
      data[result[i].key]=new Object();

      //displayObject(item);
      for(var j=0;j<item.length;j++)
      {
        var key=item[j].key;
        var value=item[j].value;

        data[result[i].key][key]=value;
      }

      //displayObject(data);

      data[result[i].key]['id']= data[result[i].key]['identifier'];
    }

    return data;
  }
  return result;
};


/**
* get config by type
*
* @return array
*/
Client.getConfigByType=function(type)
{
  var result = this.call('getConfigByType',type);
  //handle the result format
  
  //the identifier not exist
  if(exists(result) == false)
  {
    return false;
  }
  else if(exists(result.item))
  {
    // this is for only one item ,
    //result format:
    // {
    //  IDENTIFIER{
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    //  }
    // }
    //
    var data=new Object()
    result=result.item.value;
    //displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
      var key=result[i].key;
      var value=result[i].value;

      data[key]=value;
    }

    data['id']=data['identifier'];

    return data;
  
  }
  else
  {
    // this is for more then one item ,
    // there are different from one item and multi items, 
    // seems js soap client's parse problem
    //
    //displayObject(result);
    //handle the result
    //this js soap client seems strange ..
    //so handle the result to easy use format
    //result format:
    // {
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    // }
    //
    var data=new Object()
    //result=result.item.value;
    displayObject(result);

    for(var i=0; i<  result.length; i++)
    {
      var item=result[i].value;
      data[result[i].key]=new Object();

      //displayObject(item);
      for(var j=0;j<item.length;j++)
      {
        var key=item[j].key;
        var value=item[j].value;

        data[result[i].key][key]=value;
      }

      //displayObject(data);

      data[result[i].key]['id']= data[result[i].key]['identifier'];
    }

    return data;
  }


  return result;
};

/**
* get config by config identifier 
*
* @return array
*/
Client.getConfigById=function(identifier)
{
  var result = this.call('getConfigById',identifier);

  //the identifier not exist
  if(exists(result) == false)
  {
    return false;
  }
  else
  {
    //displayObject(result);
    //handle the result
    //this js soap client seems strange ..
    //so handle the result to easy use format
    //result format:
    // {
    //  id:IDENTIFIER,
    //  identifier:IDENTIFIER,
    //  value: VALUE, 
    //  type: TYPE,
    // }
    //
    var data=new Object()
    result=result.item.value;
    //displayObject(result);
    //
    data[result.item.key]=new Object();

    for(var i=0; i<  result.length; i++)
    {
      var key=result[i].key;
      var value=result[i].value;

      data[result.item.key][key]=value;
    }

    data[result.item.key]['id']=data[result.item.key]['identifier'];

    return data;
  }
};

/**
* active app instance , will deactive another actived app if exists
* 
*/
Client.activeAppInstance=function()
{
  result = this.call('activeAppInstance',identifier);
  return result;
};
var AppManager=Client;


/** App-Arena java script lib */

//var AA=new Object();
var AA=cloneAll(AppManager);

/*
AA.init=function(options,id){
};
*/

/*
AA.init=function(params){
  AppManager.init(params);
};
*/

/**
 * Save values to the current app-arena session
 * @param params json array with parameters
 * @param session_obj name of the session array, that will be updated, e.g. 'fb' to update $session->fb
 */
AA.save_to_session = function(params, session_obj,callback,failed_callback){
  params.object = session_obj; 
  var url="ajax/save_to_session.php?aa_inst_id="+aa_inst_id;
  jQuery.post(url,params,function(response){
    if(response.error == 0){
		if(isFunction(callback)){
			callback(response);
		}
    }else{
		if(isFunction(callback)){
			failed_callback(response);
		}
    }
  },'json');
};


