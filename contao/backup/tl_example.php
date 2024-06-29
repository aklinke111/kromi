<?php 
use Contao\DC_Table;


/**
 * Table tl_example
 */
$GLOBALS['TL_DCA']['tl_example'] = array
(

	// Config
    
	'config' => array
	(
                'dataContainer' => DC_Table::class,
		'enableVersioning'            => true,
		'onsubmit_callback' => array
		(
			array('tl_example', 'updates')
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'fields'                  => array('abrechnungsjahr DESC'),
			'flag'                    => 1,
			'panelLayout'             => 'filter'
		),
		'label' => array
		(
			'fields'                  => array('abrechnungsjahr', 'notiz'),
			'format'                  => '%s <span style="color:#b3b3b3; padding-left:3px;">[%s]</span>'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_example']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_example']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_example']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_example']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'abrechnungsjahr,verbrauchspreis_wasser,verbrauchspreis_abwasser,gesamtverbrauch,monatlicher_abschlag,gesamtsumme,notiz',
	),


	// Fields
	'fields' => array
	(
		'abrechnungsjahr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['abrechnungsjahr'],
			'inputType'               => 'select',
			'options_callback'        => array('tl_example', 'abrechnungsjahre'),
			'filter'     			  => true,
			'search'                  => true,
			'eval'                    => array('includeBlankOption'=>true,'mandatory'=>true, 'maxlength'=>10)
//			'save_callback' => array
//			(
//				array('tl_example', 'doppeltesAbrechnungsjahr')
//			)
		),

		'verbrauchspreis_wasser' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['verbrauchspreis_wasser'],
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>true, 'maxlength'=>10)
		),
		'verbrauchspreis_abwasser' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['verbrauchspreis_abwasser'],
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>true, 'maxlength'=>10)
		),
		'gesamtverbrauch' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['gesamtverbrauch'],
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>true, 'maxlength'=>10)
		),
		'monatlicher_abschlag' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['monatlicher_abschlag'],
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>true, 'maxlength'=>10)
		),
		'gesamtsumme' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['gesamtsumme'],
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>true, 'maxlength'=>10)
		),
		'notiz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['notiz'],
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE')
		),
       		'wasserkonstante' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_example']['wasserkonstante'],
		)
	)
);

use Contao\Backend;
class tl_example extends Backend
{


	// Auswahl der Abrechnungsjahre f r Select-Box
	public function abrechnungsjahre()
	{
	$startjahr = "2006"; // Hier das Startjahr modifizieren
	$abrechnungsjahre = array();
	$aktuellesJahr = date("Y",time());
		for($i=$startjahr;$i<=$aktuellesJahr;$i++)
		{
			$abrechnungsjahre[] = $i;
		}
	return $abrechnungsjahre;
	}



	// Doppelte Eintraege werden abgefangen
	public function doppeltesAbrechnungsjahr($varValue, DataContainer $dc)
	{
		$result = $this->Database->prepare("SELECT abrechnungsjahr FROM tl_example WHERE abrechnungsjahr=?")
								   ->execute($varValue);
		if($result->numRows){
			throw new Exception('F&uuml;r dieses Abrechnungsjahr wurden bereits Daten eingegeben!');
		} 
		else
		{
			return $varValue;
		}
	}




	public function updates(DataContainer $dc)
	{
		$this->wasserkonstante($dc);
		$this->gesamtverbrauchBerechnet();
	}


	// Berechnung der Wasserkonstanten
	public function wasserkonstante(DataContainer $dc)
	{
		//		$objwasserkonstante = $this->Database->prepare("SELECT * FROM tl_example WHERE id=?")
		//                              ->limit(1)
		//                              ->execute($dc->activeRecord->id);

		$wasser = $dc->activeRecord->verbrauchspreis_wasser;
		$abwasser = $dc->activeRecord->verbrauchspreis_abwasser;
		$gesamtverbrauch = $dc->activeRecord->gesamtverbrauch;
                $gesamtsumme = $dc->activeRecord->gesamtsumme;
		$abrechnungsjahr = $dc->activeRecord->abrechnungsjahr;

		$wasserkonstante = round( ($wasser + $abwasser) / $gesamtverbrauch, 4);	


		// Gibt es bereits Eintraege fuer das selektierte Jahr?
		$result = $this->Database->prepare("SELECT * FROM tl_example WHERE abrechnungsjahr=?")
								 ->execute($abrechnungsjahr);
		if($result->numRows)
		{
		// Es gibt einen Eintrag -> Wert in Tabelle updaten...
		$this->Database->prepare
			(
			"UPDATE tl_example 
			SET 
			tstamp=?,
			verbrauchspreis_wasser=?,
			verbrauchspreis_abwasser=?,
			gesamtverbrauch=?,
                        gesamtsumme=?,
			wasserkonstante=?
			WHERE 
			abrechnungsjahr=?"
			)
			->execute
			(
			time(),
			$wasser,
			$abwasser,
			$gesamtverbrauch,
                        $gesamtsumme,
			$wasserkonstante,
			$abrechnungsjahr
			)
			;
		}
	}



	// Gesamtverbrauch Wassergemeinschaft aus Summe Einzelverbraeuche gelesen /  Abrechnungsjahr
	function gesamtverbrauchBerechnet()
	{
		//Aktualisierung aller Werte
		$objabrechnungsjahre = $this->Database->prepare("SELECT abrechnungsjahr FROM tl_example")
							 	  ->execute();
		while ($objabrechnungsjahre->next())
		{
			$abrechnungsjahr = $objabrechnungsjahre->abrechnungsjahr;
			$result = $this->Database->prepare("SELECT SUM(verbrauch) AS gesamtverbrauch FROM wa_einzelstatistik WHERE abrechnungsjahr = ?")
							 	     ->execute($abrechnungsjahr);
			if ($result->next())
			{
				$gesamtverbrauchBerechnet = $result->gesamtverbrauch;
				$this->Database->prepare("UPDATE tl_example SET tstamp=?, gesamtverbrauch_berechnet=? WHERE abrechnungsjahr=?")
					 ->execute(time(), $gesamtverbrauchBerechnet, $abrechnungsjahr);
			}
		}
	}


}