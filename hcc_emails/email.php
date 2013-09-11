<?php
// First set up constants for email copy
define("PERSONALIZE", "Dear (name),<br /><br />");
define("CLIENT", "Greetings from the HCC team. We're pleased to share some new resources for researchers on our website.");
define("CLINICAL", "Dear colleague,<br /><br />Hello from the <a target='_blank' href='http://www.healthcommcore.org'>DF/HCC Health Communication Core.</a> 
					&nbsp; We're pleased to share some new resources for researchers on our website. We hope these will be helpful to you and your clinical
					research teams. Consulting with us is free, so let us know if you'd
					like to explore how we might help with any new or ongoing studies.");
define("DFHCC", "	Dear DF/HCC colleague,<br /><br />Hello from the <a target='_blank' href='http://www.healthcommcore.org'>DF/HCC Health Communication Core.</a> &nbsp; We're pleased to share some new resources for researchers on our website. We hope you and your team find them helpful. Free consultation and reduced rates are among your DF/HCC membership benefits, so please let us know if you'd like to discuss an upcoming project.");

$user ="";
$copy;
$qrystr;
$isEmail = FALSE;

// Check the user variable from the query string
if(isset($_GET['user'])){
	$user = htmlspecialchars($_GET['user']);
	switch($user){
	case "client":
		$copy = CLIENT;
		break;
	case "clinical":
		$copy = CLINICAL;
		break;
	case "dfhcc":
		$copy = DFHCC;
		break;
	}
	// Check if there's a source query string for viewing
	// purposes only
	if(isset($_GET['source'])){
		$qrystr = $user;
		$isEmail = TRUE;
	}
}

// Check if the kind query string is set to personalize
// for the CLIENT version of the email
if(isset($_GET['kind'])){
	if($_GET['kind'] == 'personalize'){
		$copy = PERSONALIZE . CLIENT;
	}
}
?>

 
<html>
	<head>
		<title>New resources for DF/HCC researchers</title>
	</head>
	<body>
		<table width="600" align="center" cellpadding="0" cellspacing="0"
			border="0" style="font-family:Helvetica, Arial, sans-serif; color:#555;
			font-size:15px; line-height:20px;">
		<?php if($isEmail){ ?>
			<tr>
			<td colspan="3" style="padding:18px 0 10px 0"><p style="float:right; font-size:11px">Please <a href="http://www.healthcommcore.org/hcc_emails/email.php?user=<?php echo $qrystr; ?>">click here</a> if you are unable to view this email.</p>
				</td>
		</tr>
	<?php }else{ ?>
			<tr>
				<td colspan="3" style="padding:18px 0 10px 0"></td>
			</tr>
	<?php } ?>
			<!-- banner -->
			<tr>
				<!-- left border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
				<td><img alt="HCC banner graphic"
					src="http://www.healthcommcore.org/images/stories/email_header.gif" /></td>
				<!-- right border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
			</tr>
			<tr>
				<!-- left border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
				<td style="padding:30px 68px 22px 68px; ">
					<p style="font-size:19px; color:#02529D; line-height:26px">
						<?php echo $copy; ?>
					</p>
					<p>
						<span style="font-weight:bold; color:#7C7F43; font-size:16px;">Retention of study
							participants:</span>
						How can researchers address the challenges of retaining longitudinal-study participants as they grow up? <a target="_blank"
							href="http://healthcommcore.org/index.php/resources">Learn...</a>
					</p>
					<p>
						<span style="font-weight:bold; color:#7C7F43; font-size:16px;">Customized lab
							websites:</span>
						HCC has developed a streamlined, cost-effective web design process
						to meet the needs of research labs and cores. <a target="_blank"
							href="http://healthcommcore.org/index.php/resources/207-specialized-websites-for-research-labs-and-cores">Find out...</a>
					</p>
					<p>
						<span style="font-weight:bold; color:#7C7F43; font-size:16px;">Social media:</span>
						Tweets and “likes” may offer low-cost ways to recruit the participants your study needs. <a target="_blank"
							href="http://healthcommcore.org/index.php/resources/206-using-social-media-to-achieve-research-goals">Explore...</a>
					</p>
					<p>
						<span style="font-weight:bold; color:#7C7F43; font-size:16px;">Diverse populations:</span>
						Researchers recently used HCC's services to engage with factory
						workers in Mumbai who use tobacco, young women newly diagnosed with
						breast cancer, and non-English-speaking patients in need of
						services and support. <a target="_blank"
							href="http://healthcommcore.org/index.php/our-work/print-materials">View...</a>
					</p>
					<p>
						<span style="font-weight:bold; color:#7C7F43; font-size:16px;">Free consultation:</span>
						We're always interested in hearing about your projects. To schedule
						a free consultation, contact us at &nbsp; 
						<a target="_blank" href="mailto:health_communication@dfci.harvard.edu">health_communication@dfci.harvard.edu</a> 
						or call Catherine Coleman, HCC assistant director, at (617) 632-5078.
					</p>
					<p>Please share these resources with your colleagues!</p>
				</td>	
				<!-- right border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
			</tr>
			<!-- footer -->
			<tr>
				<!-- left border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
				<td style="padding:22px 68px 30px 68px;background:#E2EBF3; color:#02529D;">
					<p>
						HCC offers a full range of professional creative services to
						researchers, clinicians, and others from Dana-Farber/Harvard Cancer
						Center, Dana-Farber Cancer Institute, National Cancer Institute,
						and other medical and research institutions in the US and
						internationally. 
					</p>		
					<p>
					A resource of the <a target="_blank" href="http://www.dfhcc.harvard.edu">Dana-Farber/Harvard Cancer Center</a>, HCC was cited
						by NCI as a "model core for the country" and received the highest
						possible ranking for its services.
					</p>
					<p>To learn more about us and our work, visit <a target="_blank" href="http://www.healthcommcore.org">www.healthcommcore.org</a>
				</td>
				<!-- right border -->
				<td width="1" style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
			</tr>
		<!-- bottom border -->
			<tr>
				<td colspan="3"style="background:#ccc;"><img alt="spacer"
					src="http://www.healthcommcore.org/images/blank.gif" /></td>
			</tr>
			<tr>
				<td colspan="3" style="padding:18px; font-size:12px;
					line-height:16px;">
					<p>To remove or add a name to this email list, please email us at 
					<a href="mailto:health_communication@dfci.harvard.edu">health_communication@dfci.harvard.edu.</a>
					</p>
					<p>
					DF/HCC Health Communication Core<br />
					Dana-Farber Cancer Institute<br />
					450 Brookline Ave LW-604<br />
					Boston, MA 02215<br />
					617 632 5078<br />
					<a target="_blank" href="http://www.healthcommcore.org">www.healthcommcore.org</a>
					</p>
				</td>	
			</tr>
			
		</table>
	</body>
</html>
