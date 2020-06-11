'use strict';

(function(){

	window.etalabCha = {

		etalabCha : this,

		init : () => {

			let elements = document.querySelectorAll('.captcha');

			elements.forEach(function(element){
				etalabCha.prepare(element);
			});

		},

		prepare : (element) => {

			etalabCha.execute(element, 'render', {}, function(element, result){

				etalabCha.render(element, result);

			});
		},

		render : (element, response) => {

			let codeHtml = '';
			if(response.checkKey)
			{
				element.setAttribute('data-checkKey', response.checkKey);

				if(response.html)
				{
					codeHtml = decodeURIComponent(response.html);
					codeHtml = codeHtml.replace(/&amp;/g, "&");
				}
			}

			element.innerHTML = codeHtml;

		},

		validation : (parent, callback) => {

			var error = 'msgError1';

			var element = parent.querySelector('.captcha');

			var params = {'checkKey' : '', 'test' : ''};
			if(element) params['checkKey'] = element.getAttribute('data-checkKey');

			var checkBoxes = parent.querySelectorAll("input[name='tx_etalabchaform_pi1[captcha][]']");
			for(let i = 0; i < checkBoxes.length; i++)
			{
				if(checkBoxes[i].checked)
				{
					params['test'] += '' + checkBoxes[i].value;
				}
			}

			if(params['checkKey'] && params['test'])
			{
				etalabCha.execute(element, 'validate', params, function(element, result){

					error = '';

					if(!result.response)
					{
						error = 'msgError2';
						etalabCha.render(element, result);
					}

                    if(typeof(callback) === "function")
                    {
                        callback(result.response, error);
                    }

				});
			}
			else
			{
                if(typeof(callback) === "function")
                {
                    callback(false, error);
                }	
			}
		},

		execute : (element, action, params, callback) => {

            if(action)
            {
	            var posts = {
	                'action' : action,
	            };

	            if(typeof params != 'undefined')
	            {
	            	if(Object.keys(params).length)
	            	{
						Object.keys(params).forEach(function(key){
						    posts[key] = params[key];
						});
	            	}
	            }

	            posts = JSON.stringify(posts);

				// Old compatibility code, no longer needed.
				// Mozilla, Safari, IE7+ ...
				if (window.XMLHttpRequest)
				{
				    var httpRequest = new XMLHttpRequest();
				}
				// IE 6 and older
				else if (window.ActiveXObject)
				{ 
				    var httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
				}

			    if (httpRequest)
			    {
					httpRequest.onreadystatechange = (e) => {

						if(httpRequest.readyState === XMLHttpRequest.DONE)
						{
							if (httpRequest.status === 200)
							{
								var response = JSON.parse(httpRequest.responseText);

	                            if(typeof(callback) === "function")
	                            {
	                                callback(element, response);
	                            }
							} 
							else
							{
								alert('There was a problem with the request.');
							}
						}
					};
				    httpRequest.open('POST', '/Assets/Captcha/Captcha.php', true);
				    httpRequest.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
					httpRequest.setRequestHeader("Content-length", posts.length);
					httpRequest.setRequestHeader("Connection", "close");		    
				    //httpRequest.overrideMimeType("application/json");
				    httpRequest.send(posts);
			    }
			}
		},

	};

})();

document.addEventListener('DOMContentLoaded', function(event){
    window.etalabCha.init();
});
