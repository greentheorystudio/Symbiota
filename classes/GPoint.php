<?php
define ('meter2nm', (1/1852));
define ('nm2meter', 1852);

class GPoint
{
	private $ellipsoid = array(
	'Airy' =>array (6377563, 0.00667054),
	'Australian National' =>array	(6378160, 0.006694542),
	'Bessel 1841' =>array	(6377397, 0.006674372),
	'Bessel 1841 Nambia' =>array	(6377484, 0.006674372),
	'Clarke 1866' =>array	(6378206, 0.006768658),
	'Clarke 1880' =>array	(6378249, 0.006803511),
	'Everest' =>array	(6377276, 0.006637847),
	'Fischer 1960 Mercury' =>array (6378166, 0.006693422),
	'Fischer 1968' =>array (6378150, 0.006693422),
	'GRS 1967' =>array	(6378160, 0.006694605),
	'GRS 1980' =>array	(6378137, 0.00669438),
	'Helmert 1906' =>array	(6378200, 0.006693422),
	'Hough' =>array	(6378270, 0.00672267),
	'International' =>array	(6378388, 0.00672267),
	'Krassovsky' =>array	(6378245, 0.006693422),
	'Modified Airy' =>array	(6377340, 0.00667054),
	'Modified Everest' =>array	(6377304, 0.006637847),
	'Modified Fischer 1960' =>array	(6378155, 0.006693422),
	'South American 1969' =>array	(6378160, 0.006694542),
	'WGS 60' =>array (6378165, 0.006693422),
	'WGS 66' =>array (6378145, 0.006694542),
	'WGS 72' =>array (6378135, 0.006694318),
	'WGS 84' =>array (6378137, 0.00669438));

	private $a;
    private	$e2;
    private	$datum;
    private $lat;
    private $long;
    private $utmNorthing;
    private $utmEasting;
    private $utmZone;

    public function __construct($datum)
	{
		$this->setDatum($datum);
	}
	
	public function setDatum($datum): void
    {
		if(preg_match('/nad\s*83/i',$datum)){
			$datum = 'GRS 1980';
		}
		elseif(preg_match('/nad\s*27/i',$datum)){
			$datum = 'Clarke 1866';
		}
		else{
			$datum = 'WGS 84';
		}
		$this->a = $this->ellipsoid[$datum][0];
		$this->e2 = $this->ellipsoid[$datum][1];
		$this->datum = $datum;
	}

    public function Lat() {
	    return $this->lat;
	}

	public function Long() {
	    return $this->long;
	}

    public function setUTM($easting, $northing, $zone = null): void
    {
		$this->utmNorthing = $northing;
		$this->utmEasting = $easting;
		$this->utmZone = $zone;
	}

	public function N() {
	    return $this->utmNorthing;
	}

	public function E() {
	    return $this->utmEasting;
	}

	public function Z() {
	    return $this->utmZone;
	}

    public function convertTMtoLL($LongOrigin = null): void
    {
		$k0 = 0.9996;
		$e1 = (1-sqrt(1-$this->e2))/(1+sqrt(1-$this->e2));
		$falseEasting = 0.0;
		$y = $this->utmNorthing;

		if (!$LongOrigin){
			sscanf($this->utmZone, '%d%s',$ZoneNumber,$ZoneLetter);
			$isSouthern = false;
			if($ZoneLetter){
				if(strtoupper($ZoneLetter) < 'N'){
					$isSouthern = true;
				}
				if(strtoupper($ZoneLetter) === 'S'){
					if(($ZoneNumber > 18 && $ZoneNumber < 23) || $y < 3540000 || $y > 4420000){
						$isSouthern = true;
					}
				}
			}			
			if($isSouthern){
				$y -= 10000000.0;
			}
			$LongOrigin = ($ZoneNumber - 1)*6 - 180 + 3;
			$falseEasting = 500000.0;
		}

		$x = $this->utmEasting - $falseEasting; //remove 500,000 meter offset for longitude

		$eccPrimeSquared = ($this->e2)/(1-$this->e2);

		$M = $y / $k0;
		$mu = $M/($this->a*(1-$this->e2/4-3*$this->e2*$this->e2/64-5*$this->e2*$this->e2*$this->e2/256));

		$phi1Rad = $mu	+ (3*$e1/2-27*$e1*$e1*$e1/32)*sin(2*$mu) 
					+ (21*$e1*$e1/16-55*$e1*$e1*$e1*$e1/32)*sin(4*$mu)
					+(151*$e1*$e1*$e1/96)*sin(6*$mu);

		$N1 = $this->a/sqrt(1-$this->e2*sin($phi1Rad)*sin($phi1Rad));
		$T1 = tan($phi1Rad)*tan($phi1Rad);
		$C1 = $eccPrimeSquared*cos($phi1Rad)*cos($phi1Rad);
		$R1 = $this->a*(1-$this->e2)/ ((1 - $this->e2 * sin($phi1Rad) * sin($phi1Rad)) ** 1.5);
		$D = $x/($N1*$k0);

		$tlat = $phi1Rad - ($N1*tan($phi1Rad)/$R1)*($D*$D/2-(5+3*$T1+10*$C1-4*$C1*$C1-9*$eccPrimeSquared)*$D*$D*$D*$D/24
						+(61+90*$T1+298*$C1+45*$T1*$T1-252*$eccPrimeSquared-3*$C1*$C1)*$D*$D*$D*$D*$D*$D/720); // fixed in 1.1
		$this->lat = rad2deg($tlat);

		$tlong = ($D-(1+2*$T1+$C1)*$D*$D*$D/6+(5-2*$C1+28*$T1-3*$C1*$C1+8*$eccPrimeSquared+24*$T1*$T1)
						*$D*$D*$D*$D*$D/120)/cos($phi1Rad);
		$this->long = $LongOrigin + rad2deg($tlong);
	}
}
