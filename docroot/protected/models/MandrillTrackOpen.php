<?php
/**
 * Class MandrillTrackOpen
 *
 * @property Int    $id
 * @property String $emailId
 * @property String $opened
 * @property String $mobile
 * @property String $os_company
 * @property String $os_company_url
 * @property String $os_family
 * @property String $os_icon
 * @property String $os_name
 * @property String $os_url
 * @property String $type
 * @property String $ua_company
 * @property String $ua_company_url
 * @property String $ua_family
 * @property String $ua_icon
 * @property String $ua_name
 * @property String $ua_url
 * @property String $ua_version
 * @property String $country_short
 * @property String $country_long
 * @property String $region
 * @property String $timezone
 * @property String $latitude
 * @property String $longitude
 *
 *
 *
 */
class MandrillTrackOpen extends CActiveRecord
{
	/**
	 * @param string $className
	 * @return static
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function relations()
	{
		return [
			'email' => [self::BELONGS_TO, 'MandrillEmail', 'emailId']
		];
	}

	public function rules()
	{
		return array(
			array(
				'id,emailId,opened,mobile,os_company,os_company_url,os_family,os_icon,os_name,os_url,type,ua_company,ua_company_url,ua_family,ua_icon,ua_name,ua_url,ua_version,country_short,country_long,region,timezone,latitude,longitude',
				'safe'
			)
		);
	}

	public function tableName()
	{
		return 'mandrillTrackOpen';
	}

	public function attributeLabels()
	{
		return array(
			'id'             => 'Id',
			'emailId'        => 'EmailId',
			'opened'         => 'Opened',
			'mobile'         => 'Mobile',
			'os_company'     => 'OS Company',
			'os_company_url' => 'OS Company url',
			'os_family'      => 'OS Family',
			'os_icon'        => 'OS Icon',
			'os_name'        => 'OS Name',
			'os_url'         => 'OS Url',
			'type'           => 'Type',
			'ua_company'     => 'UA Company',
			'ua_company_url' => 'UA Company url',
			'ua_family'      => 'UA Family',
			'ua_icon'        => 'UA Icon',
			'ua_name'        => 'UA Name',
			'ua_url'         => 'UA Url',
			'ua_version'     => 'UA Version',
			'country_short'  => 'Country short',
			'country_long'   => 'Country long',
			'region'         => 'Region',
			'timezone'       => 'Timezone',
			'latitude'       => 'Latitude',
			'longitude'      => 'Longitude',
		);
	}

}
