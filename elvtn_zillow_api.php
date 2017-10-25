<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class for calling Zillow APIs documented here:
 *
 * https://www.zillow.com/mortgage/api/
 *
 * These APIs require a partnerId, which can be obtained from Zillow support. Each call
 * also takes a lenderRef, which is assumed to be the Zillow screen name.
 */
class Elvtn_Zillow_Api
{
	private $partnerId;

	public function __construct($partnerId = null)
	{
		if ( ! empty($partnerId))
		{
			$this->partnerId = $partnerId;
		}
	}

	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	public function __get($property)
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
	}

	/**
	 * Calls the Zillow getPublishedLenderReviews API and returns a block of HTML
	 * with latest Zillow reviews for the given lenderRef.
	 * 
	 * $screenName: Zillow screen name for which to get reviews.
	 * $numReviews: Number of reviews (default 5)
	 */
	public function getPublishedLenderReviews($screenName , $numReviews = 5)
	{
		if ( empty($this->partnerId) )
		{
			throw new Exception('Zillow partnerId is required.');
		}

		if( empty($screenName) )
		{
			throw new Exception('Zillow screenName is required.');
		}

		$params = [
			"partnerId" => $this->partnerId,
			"lenderRef" => ["screenName" => $screenName],
			"fields"    => ["reviewerName" , "created" , "rating" , "content"],
			"pageSize"  => (int)$numReviews
		];

		$url = "https://mortgageapi.zillow.com/getPublishedLenderReviews";

        $response = wp_remote_post(
            $url,
            array(
                'body' => json_encode($params)
            )
        );

		if ( is_wp_error( $response ) )
		{
			return "<p>Error retrieving Zillow reviews</p>";
		}
		else
		{
			return $this->convert_getPublishedLenderReviews_to_html($response['body'], $screenName);
		}
	}

	/**
	 * Converts the JSON output of getPublishedLenderReviews to HTML.
	 */
	private function convert_getPublishedLenderReviews_to_html($json, $screenName = NULL)
	{
		$obj = json_decode($json, true, JSON_NUMERIC_CHECK);

		if($obj == NULL || $obj['reviews'] == NULL)
		{
			return "<p class=\"elvtn-zillow-pro-review-error\">Unable to load Zillow reviews.</p>";
		}

		$html = "<div class=\"elvtn-zillow-pro-reviews\">";
		foreach($obj['reviews'] as $review)
		{
			// Pull out MM/DD/YYYY from the date/time field
			$reviewYear = substr($review['created'], 0, 4);
			$reviewMon  = substr($review['created'], 5, 2);
			$reviewDay  = substr($review['created'], 8, 2);

			$html .= "<div class=\"elvtn-zillow-pro-review\">";
			$html .= "<h4 class=\"elvtn-zillow-pro-review-meta\">" . $review['rating'] . " stars on " .  intval($reviewMon) . "/" . intval($reviewDay) . "/" . $reviewYear . "</h4>";
			$html .= "<p class=\"elvtn-zillow-pro-review-description\"><i>" . $review['content'] . "</i></p>";
			$html .= "<hr class=\"elvtn-zillow-pro-review-line\"/></div>";
		}

		if($obj['totalReviews'] != NULL && $screenName != NULL)
		{
			$html .= "<p>See all <a href=\"https://www.zillow.com/lender-profile/" . $screenName . "/#reviews\" target=\"_blank\">" . $obj['totalReviews'] . " Reviews</a> on Zillow</p>";
		}

		$html .= "</div>";

		return $html;
	}
}

?>