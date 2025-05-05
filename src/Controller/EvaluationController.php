<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends AbstractController
{
  #[Route('/evaluation/add', name: 'evaluation_add')]
  public function add(Request $request, EntityManagerInterface $entityManager): Response
  {
    $evaluation = new Evaluation();
    $form = $this->createForm(EvaluationType::class, $evaluation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($evaluation);
      $entityManager->flush();

      return $this->redirectToRoute('evaluation_add');
    }

    return $this->render('evaluation/addEvaluation.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  #[Route('/evaluation/list', name: 'evaluation_list')]
  public function list(Request $request, EntityManagerInterface $entityManager): Response
  {
    // Create query builder
    $queryBuilder = $entityManager->getRepository(Evaluation::class)
      ->createQueryBuilder('e')
      ->leftJoin('e.idJury', 'j')
      ->leftJoin('e.idHackathon', 'h')
      ->leftJoin('e.idProjet', 'p')
      ->leftJoin('e.votes', 'v');

    // Search functionality
    $search = $request->query->get('search');
    if ($search) {
      $queryBuilder->andWhere(' 
            p.id LIKE :search 
        ')
        ->setParameter('search', '%' . $search . '%');
    }

    // Sorting
    $sort = $request->query->get('sort', 'e.id');
    $direction = $request->query->get('direction', 'asc');

    $validSorts = ['e.id', 'j.id', 'h.id', 'p.id', 'e.noteTech', 'e.noteInnov', 'e.date'];
    $sort = in_array($sort, $validSorts) ? $sort : 'e.id';

    $queryBuilder->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

    // Get paginated results
    $evaluations = $queryBuilder->getQuery()->getResult();

    // Map votes for each evaluation
    $evaluationVoteMap = [];
    foreach ($evaluations as $evaluation) {
      $evaluationVoteMap[$evaluation->getId()] = $evaluation->getVotes();
    }

    return $this->render('evaluation/listEvaluation.html.twig', [
      'evaluations' => $evaluations,
      'evaluationVoteMap' => $evaluationVoteMap,
      'search' => $search,
      'sort' => $sort,
      'direction' => $direction
    ]);
  }

  #[Route('/evaluation/edit/{id}', name: 'evaluation_edit')]
  public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
  {
    $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);

    if (!$evaluation) {
      throw $this->createNotFoundException('Evaluation not found');
    }

    $form = $this->createForm(EvaluationType::class, $evaluation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();
      $this->addFlash('success', 'Evaluation updated successfully!');
      return $this->redirectToRoute('evaluation_list');
    }

    return $this->render('evaluation/editEvaluation.html.twig', [
      'form' => $form->createView(),
      'evaluation' => $evaluation
    ]);
  }

  #[Route('/evaluation/delete/{id}', name: 'evaluation_delete')]
  public function delete(EntityManagerInterface $entityManager, int $id): Response
  {
    $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);

    if ($evaluation) {
      $entityManager->remove($evaluation);
      $entityManager->flush();
      $this->addFlash('success', 'Evaluation deleted successfully!');
    }

    return $this->redirectToRoute('evaluation_list');
  }
  #[Route('/top-projects', name: 'app_top_projects')]
  public function topProjects(EntityManagerInterface $em): Response
  {
    // First get all project scores
    $projectScores = $em->createQueryBuilder()
      ->select([
        'p.id',
        'AVG(e.noteTech) as averageTech',
        'AVG(e.noteInnov) as averageInnov',
        '(AVG(e.noteTech) + AVG(e.noteInnov)) as totalScore',
        'COUNT(v.id) as voteCount'
      ])
      ->from('App\Entity\Evaluation', 'e')
      ->join('e.idProjet', 'p')
      ->leftJoin('e.votes', 'v')
      ->groupBy('p.id')
      ->getQuery()
      ->getResult();

    // Sort by totalScore descending
    usort($projectScores, function ($a, $b) {
      return $b['totalScore'] <=> $a['totalScore'];
    });

    // Get top 3 projects
    $topProjects = array_slice($projectScores, 0, 3);

    // Calculate max score
    $maxScore = 0;
    foreach ($projectScores as $project) {
      if ($project['totalScore'] > $maxScore) {
        $maxScore = $project['totalScore'];
      }
    }

    // Get basic statistics
    $stats = $em->createQueryBuilder()
      ->select([
        'COUNT(e.id) as totalEvaluations',
        'AVG(e.noteTech) as averageTechScore',
        'AVG(e.noteInnov) as averageInnovScore'
      ])
      ->from('App\Entity\Evaluation', 'e')
      ->getQuery()
      ->getSingleResult();

    // Calculate closest competition
    $scores = array_column($projectScores, 'totalScore');


    // Calculate participation rate
    $totalJury = $em->createQueryBuilder()
      ->select('COUNT(j.id)')
      ->from('App\Entity\Jury', 'j')
      ->getQuery()
      ->getSingleScalarResult();

    $participatingJury = $em->createQueryBuilder()
      ->select('COUNT(DISTINCT e.idJury)')
      ->from('App\Entity\Evaluation', 'e')
      ->getQuery()
      ->getSingleScalarResult();

    $participationRate = $totalJury > 0 ? round(($participatingJury / $totalJury) * 100, 1) : 0;

    return $this->render('evaluation/top_projects.html.twig', [
      'topProjects' => $topProjects,
      'maxScore' => $maxScore ?: 1,
      'totalEvaluations' => $stats['totalEvaluations'],
      'averageTechScore' => $stats['averageTechScore'] ?: 0,
      'averageInnovScore' => $stats['averageInnovScore'] ?: 0,
      'participationRate' => $participationRate,
      'topScore' => $maxScore ?: 0
    ]);
  }
}
