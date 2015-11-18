<?php
session_start();
require("../global.php"); 
require("../secure.php"); 
$pageTitle = "Documents, Forms and Instructions";
echo html_header($pageTitle);
?>
<h2><?php echo $pageTitle;?></h2>
<p><strong>Documents &amp; Forms</strong> (pdf documents require Acrobat Reader)</p>
<blockquote> 
  <p><a href="Manual.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Manual</strong></a> (pdf)<br>
  The Wooster &amp; Stock Technical Manual, for general reference</p>
  <p><a href="ValuationForm.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Valuation Form</strong></a> (pdf) - print 
    on pink paper<br>
    The valuation form should be taken by valuer on all valuations 
    and completed in as much detail as possible.</p>
  <p><a href="TermsofBusiness.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Terms of Business</strong></a> (pdf) 
    - print on headed paper<br>
    Terms of business only, to be given to vendor at point of 
    valuation.</p>
  <p><a href="PropertyParticularsForm.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Sales Property Particulars 
    Form</strong></a> (pdf) - print on green paper<br>
    The particulars form is to be taken when photos and floorplans are done and 
    completed by the vendor if present. This form repeats some questions from 
    the valuation form, therefore can be part-completed prior to the visit. If 
    the property is to be &quot;fast-tracked&quot;, the form must be filled in 
    completely by the vendor.</p>
  <p><a href="LettingsForm.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Lettings Property Particulars 
    Form</strong></a> (pdf) - print on green paper<br>
    The particulars form is to be taken when photos and floorplans are done and 
    completed by the vendor if present. This form repeats some questions from 
    the valuation form, therefore can be part-completed prior to the visit. If 
    the property is to be &quot;fast-tracked&quot;, the form must be filled in 
    completely by the vendor.</p>
  <p><a href="floorplanguide.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Floorplan Guide</strong></a> (pdf)<br>
    Template form for drawing floorplans</p>
  <p><a href="expenses.pdf"><strong><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Expenses Form</strong></a> (pdf)<br>
    So you no longer need to hassle Keith for one </p>
  <p>&nbsp;</p>
</blockquote>
<p><strong>Instructions and Help</strong></p>
<blockquote>
  <p><a href="technicalsupport.php"><strong>Technical Support contacts</strong></a><br>
    Various support contacts and details for when Mark is away or unavailable</p>
  <p><strong><a href="http://www.woosterstock.co.uk/admin/folder/folder.htm">Folder 
    Structure</a></strong><br>
    An overview of how to use the SHARE folder structure (currently only applicable 
    to lettings)</p>
</blockquote>
<p>&nbsp;</p>
<p><strong>Other</strong></p>
<blockquote>
  <p><a href="http://www.ricsmea.com" target="_blank">RICS Manual</a><br>
  Username: 332848<br>
  Password: WEmlhyI</p>
  <p><a href="listed_buildings.php">Listed Buildings</a></p>
  <p><a href="commercial_classes.php">Commercial Usage Classes</a></p>
  <p><a href="wards.php">Wards, Stamp Duty Exemption</a></p>
  <p><a href="AreaOverview.pdf"><img src="/images/icons/pdf.gif" width="16" height="16" hspace="2" vspace="2" border="0" align="absmiddle">Area Overview</a> (printable)</p>
</blockquote>
</body>
</html>
