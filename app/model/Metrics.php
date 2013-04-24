<?php


class Metrics {

	private $db;

	public function __construct( Nette\Database\Connection $db ) {
		$this->db = $db;
	}

	public function getMetrics() {
		return $this->db
			->table( 'metrics' )
			->order( 'name' );
	}


	public function getMetricsId( $name ) {
		return  $this->db
			->table( 'metrics' )
			->where( 'name', $name )
			->fetch()->id;
	}


	public function getMetricDiffs( $metricId, $task1, $task2 ) {
		$scores = new ZipperIterator( array(
			$this->getScoresForTranslations( $metricId, $task1 ),
			$this->getScoresForTranslations( $metricId, $task2 )
		) );

		$diffScores = array();
		foreach( $scores as $score ) {
			$diffScores[] = $score[0][ 'score' ] - $score[1][ 'score' ];
		}

		sort( $diffScores );
		return $diffScores;
	}


	private function getScoresForTranslations( $metricId, $taskId ) {
		return $this->db
			->table( 'translations_metrics' )
			->select( 'score' )
			->where( 'metrics_id', $metricId )
			->where( 'tasks_id', $taskId )
			->order( 'translations.sentences_id' );
	}
}