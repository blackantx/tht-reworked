<br />This is the email a NON-RESELLER client gets when they first go though the order form and complete it. This email should contain all they're details.
This does not go out when admin validation is required.  Use the "Awaiting Validation" template for that.  The confirmation link enables them
to login to THT,but not cPanel and only works when admin validation is needed.<br />
<br />
<b>Template Variables:</b><br />
%USER% - Client's username<br />
%FNAME% - Client's first name<br />
%LNAME% - Client's last name<br />
%PASS% - Client's password<br />
%EMAIL% - Client's email<br />
%DOMAIN% - The clients package url<br />
%PACKAGE% - The package the client signed up for<br />
%NAMESERVERS% - The nameservers for the server the account was created on<br />
%SERVERIP% - The ip of the server the account is on.  (Useful for temporary login info)<br />
%CPPORT% - The port for the client's control panel.  (Ex. cPanel)<br />
<br />
Available, but not usefulfor this email:<br />
<br />
%CONFIRM% - The account confirmation link  (See description for more details.)<br />
%RESELLERPORT% - The port for the client's reseller control panel.
