<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>jQuery notification Plug in</title>
        <link href="css/jquery_notification.css" type="text/css" rel="stylesheet"/>

        <script type="text/javascript" src="js/jquery_v_1.4.js"></script>
        <script type="text/javascript" src="js/jquery_notification_v.1.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){ 
            });
        </script>
    </head>
    <body>
        <div class="container">
		<h1>jQuery notification Plug-in</h1> 
		   <a href='http://9lessons.info'>9lessons.info</a> - <a href='http://androidhive.info'>Ravi Tamada</a>
            <h2>The Basic Setup</h2>

            <ul class="ul_points">
                <li>
                    Include the jQuery and notification libraries into your document
                    <pre class="javascript">
&lt;script type=&quot;text/javascript&quot; src=&quot;js/jquery_v_1.4.js&quot;&gt;&lt;/script&gt;
&lt;script type=&quot;text/javascript&quot; src=&quot;js/jquery_notification_v.1.js&quot;&gt;&lt;/script&gt;
                    </pre>
                </li>
                <li>
                    Include the notification CSS into your document
                    <pre class="javascript">
&lt;link href=&quot;css/jquery_notification.css&quot; type=&quot;text/css&quot; rel=&quot;stylesheet&quot;/&gt;
                    </pre>
                </li>
                <li>
                    Call this showNotification() function in by passing parameters
                    <pre class="javascript">
showNotification();
                    </pre>
                </li>
            </ul>

            <h2>Usage:</h2>
            <ul class="ul_points">
                <li>
                    <h3>Changing Type of notification</h3>  
                    This plug in supports four types of major notifications <em>(Success, Error, Warning and Information)</em>.
                    To change the notification type pass parameter type="value" to showNotification() function.
                    <ul class="ol_points">
                        <li>
                            Showing Success notification (pass parameter type = "success")
                            <pre class="javascript">
                                showNotification({
                                    message: &quot;This is a sample Success notification&quot;,
                                    type: &quot;success&quot;
                                });
                            </pre>
                            Demo: <input type="button" class="button" value="Show Success Message" onclick="return showSuccessMessage()"/>
                            <script type="text/javascript">
                                function showSuccessMessage(){
                                    showNotification({
                                        type : "success",
                                        message: "This is a sample success notification"
                                    });    
                                }                                
                            </script>
                        </li>
                        <li>
                            Showing Error notification (pass parameter type = "error")
                            <pre class="javascript">
                                showNotification({
                                    message: &quot;This is a sample Error notification!&quot;,
                                    type: &quot;error&quot;
                                });
                            </pre>
                            Demo: <input type="button" class="button"  value="Show Error Message" onclick="return showErrorMessage()"/>
                            <script type="text/javascript">
                                function showErrorMessage(){
                                    showNotification({
                                        type : "error",
                                        message: "This is a sample success notification"
                                    });    
                                }                                
                            </script>
                        </li>
                        <li>
                            Showing Warning notification (pass parameter type = "warning")
                            <pre class="javascript">
                                showNotification({
                                    message: &quot;This is a sample Warning notification&quot;,
                                    type: &quot;warning&quot;
                                });
                            </pre>
                            Demo: <input type="button"  class="button" value="Show Warning Message" onclick="return showWarningMessage()"/>
                            <script type="text/javascript">
                                function showWarningMessage(){
                                    showNotification({
                                        type : "warning",
                                        message: "This is a sample Warning notification"
                                    });    
                                }                                
                            </script>
                        </li>
                        <li>
                            Showing Information notification (pass parameter type = "information")
                            <pre class="javascript">
                                showNotification({
                                        type : &quot;warning&quot;,
                                        message: &quot;This is a sample Warning notification&quot;
                                });
                            </pre>
                            Demo: <input type="button"  class="button" value="Show Information Message" onclick="return showInformationMessage()"/>
                            <script type="text/javascript">
                                function showInformationMessage(){
                                    showNotification({
                                        type : "information",
                                        message: "This is a sample Warning notification"
                                    });    
                                }                                
                            </script>
                        </li>
                    </ul>
                </li>
                <li>
                    <h3>Auto Close Notification after some seconds</h3>  
                    To make notification close automatically after some seconds pass parameter autoClose = true and duration parameter.
                    <pre class="javascript">
                        showNotification(){
                            message: &quot;This is Auto Close notification. Message will close after 2 seconds&quot;,
                            autoClose: true,
                            duration: 2
                        }
                    </pre>
                    Demo: <input type="button"  class="button" value="Show Auto Close Notification" onclick="return showAutoCloseMessage()"/>
                    <script type="text/javascript">
                        function showAutoCloseMessage(){
                            showNotification({
                                message: "This is Auto Close notification. Message will close after 2 seconds",
                                autoClose: true,
                                duration: 2
                            });    
                        }                                
                    </script>
                </li>
                <li>
                    <h3>Showing Notification after some seconds</h3>  
                    You can delay showing message some seconds once the document is ready. Pass parameter startAfter to function with specific seconds
                    <pre class="javascript">
                        showNotification(){
                            message: &quot;This is Auto Close notification. Message will close after 2 seconds&quot;,
                            autoClose: true,
                            duration: 2
                        }
                    </pre>
                    Demo: <input type="button"  class="button" value="Show After some seconds" onclick="return showAfterWaitMessage()"/>
                    <script type="text/javascript">
                        function showAfterWaitMessage(){
                            showNotification({
                                message: "This message will be shown after 2 seconds",
                                showAfter: 2
                            });    
                        }                                
                    </script>
                </li>
            </ul>
            <br/><br/>
            <h2>Using notification plugin with Ajax Calls</h2>
            Below is an example of using this plug in with Ajax calls
            <pre class="javascript">
                $.ajax({
                    type: &quot;POST&quot;,
                    data: post_parmas,
                    url: &quot;sample.php&quot;,
                    error: function (msg) {
                        // Error occurred in sending request
                        showNotification({
                            message: &quot;Oops! an error occurred.&quot;,
                            type: &quot;error&quot; // type of notification is error
                            autoClose: true, // auto close to true
                            duration: 5 // display duration
                        });
                    },
                    success: function (msg) {
                        showNotification({
                            message: &quot;Show Ajax result message here!&quot;,
                            type: &quot;error&quot; // type of notification is error/success/warning/information,
                            autoClose: true, // auto close to true
                            duration: 5 // message display duration
                        });
                    }
                });
            </pre>


            <br/><br/>
            <h2>Using with PHP</h2>
            Below is an example of using plugin with PHP code. The following code will display a message by reading PHP Get parameters.
            <br/>Always place this code at the end of your page.
            <pre class="php">
                &lt;?php
                if (isset($_GET["type"])) {
                    $message = $_GET[&quot;message&quot;];
                    $type = $_GET[&quot;type&quot;];
                    ?&gt;
                     &lt;script type=&quot;text/javascript&quot;&gt;
                        showNotification({
                            message: &quot;&lt;?php echo $message; ?&gt;&quot;,
                            type: &quot;&lt;?php echo $type; ?&gt;&quot;,
                            autoClose: true,
                            duration: 5                                        
                        });
                    &lt;/script&gt;
                    &lt;?php
                }
                ?&gt;
            </pre>
            <br/>
            Demo: <a href="?type=error&message=This is sample PHP get parameters reading notification"><input type="button" class="button" value="Show PHP Demo"/></a>
                
        </div>
        <br/><br/><br/>
    </body>
</html>
