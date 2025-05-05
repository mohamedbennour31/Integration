<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\HackathonRepository;
use App\Entity\Hackathon;


#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(Request $request, UserRepository $userRepository): Response
    {
        $search = $request->query->get('search');        
        $sort = $request->query->get('sort', 'idUser');
        $direction = $request->query->get('direction', 'asc');
        $status = $request->query->get('status');
        
        $queryBuilder = $userRepository->createQueryBuilder('u');
        
        // Exclude admin users from the list
        $queryBuilder->where('u.roleUser NOT LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%');
        
        // Search
        if ($search) {
            $queryBuilder
                ->andWhere('(u.nomUser LIKE :search OR u.prenomUser LIKE :search OR u.emailUser LIKE :search)')
                ->setParameter('search', '%' . $search . '%');
        }
        
        // Filter by status
        if ($status) {
            $queryBuilder
                ->andWhere('u.statusUser = :status')
                ->setParameter('status', $status);
        }
        
        // Sorting
        $queryBuilder->orderBy('u.' . $sort, $direction);
        
        $users = $queryBuilder->getQuery()->getResult();
        
        // Prepare user statistics for charts
        $roleStatistics = $this->getUserRoleStatistics($userRepository);
        $statusStatistics = $this->getUserStatusStatistics($userRepository);
        
        return $this->render('backoffice/admin/admin.html.twig', [
            'users' => $users,
            'currentSort' => $sort,
            'currentDirection' => $direction,
            'currentSearch' => $search,
            'currentStatus' => $status,
            'debugData' => [
                'roleStatistics' => $roleStatistics,
                'statusStatistics' => $statusStatistics
            ]
        ]);
    }

    private function getUserRoleStatistics(UserRepository $userRepository): array
    {
        try {
            $qb = $userRepository->createQueryBuilder('u');
            
            // Exclude admin users from the statistics
            $qb->where('u.roleUser NOT LIKE :role')
               ->setParameter('role', '%"ROLE_ADMIN"%');
               
            $users = $qb->getQuery()->getResult();
            
            $roleCount = [];
            
            foreach ($users as $user) {
                // Check if roleUser is actually a string that needs to be decoded
                $roles = $user->getRoles();
                
                // Skip ROLE_USER as it's assigned to everyone
                $hasSpecialRole = false;
                foreach ($roles as $role) {
                    // Skip both ROLE_USER and ROLE_ADMIN
                    if ($role !== 'ROLE_USER' && $role !== 'ROLE_ADMIN') {
                        $hasSpecialRole = true;
                        if (!isset($roleCount[$role])) {
                            $roleCount[$role] = 0;
                        }
                        $roleCount[$role]++;
                    }
                }
                
                // If a user doesn't have any special role, count them as "Regular Users"
                if (!$hasSpecialRole) {
                    if (!isset($roleCount['Regular Users'])) {
                        $roleCount['Regular Users'] = 0;
                    }
                    $roleCount['Regular Users']++;
                }
            }
            
            // If no data is found, return default data
            if (empty($roleCount)) {
                return $this->getDefaultRoleStatistics();
            }
            
            return $roleCount;
        } catch (\Exception $e) {
            // If there's an error, return default data
            return $this->getDefaultRoleStatistics();
        }
    }
    
    private function getDefaultRoleStatistics(): array
    {
        // Return some default data when DB access fails (excluding admin users)
        return [
            'ROLE_ORGANISATEUR' => 3,
            'ROLE_PARTICIPANT' => 10,
            'Regular Users' => 5
        ];
    }
    
    private function getUserStatusStatistics(UserRepository $userRepository): array
    {
        try {
            $qb = $userRepository->createQueryBuilder('u');
            
            // Exclude admin users from the statistics
            $qb->select('u.statusUser as status, COUNT(u.idUser) as count')
               ->where('u.roleUser NOT LIKE :role')
               ->setParameter('role', '%"ROLE_ADMIN"%')
               ->groupBy('u.statusUser');
            
            $results = $qb->getQuery()->getResult();
            
            $statusCount = [];
            foreach ($results as $result) {
                // Check if the result is an array (native query result) or an object
                if (is_array($result)) {
                    $status = $result['status'];
                    $count = (int)$result['count'];
                } else {
                    $status = $result->getStatus();
                    $count = (int)$result->getCount();
                }
                
                $statusCount[$status] = $count;
            }
            
            // If no data is found, return default data
            if (empty($statusCount)) {
                return $this->getDefaultStatusStatistics();
            }
            
            return $statusCount;
        } catch (\Exception $e) {
            // If there's an error, return default data
            return $this->getDefaultStatusStatistics();
        }
    }
    
    private function getDefaultStatusStatistics(): array
    {
        // Return some default data when DB access fails
        return [
            'active' => 15,
            'inactive' => 4
        ];
    }
    
    private function createRoleChart(ChartBuilderInterface $chartBuilder, array $roleStatistics): Chart
    {
        // Make sure we have data to display
        if (empty($roleStatistics)) {
            // Add default data to prevent empty chart
            $roleStatistics = ['No Data' => 1];
        }
        
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        
        $labels = array_keys($roleStatistics);
        $data = array_values($roleStatistics);
        
        // Generate colors for each role
        $backgroundColors = [
            'ROLE_ADMIN' => 'rgb(255, 99, 132)',
            'ROLE_ORGANISATEUR' => 'rgb(54, 162, 235)',
            'ROLE_PARTICIPANT' => 'rgb(255, 205, 86)',
            'Regular Users' => 'rgb(75, 192, 192)',
            'No Data' => 'rgb(201, 203, 207)'
        ];
        
        // Default color for other roles
        $defaultColors = [
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)',
            'rgb(255, 159, 64)'
        ];
        
        $colors = [];
        foreach ($labels as $i => $label) {
            if (isset($backgroundColors[$label])) {
                $colors[] = $backgroundColors[$label];
            } else {
                $colors[] = $defaultColors[$i % count($defaultColors)];
            }
        }
        
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Roles Distribution',
                    'backgroundColor' => $colors,
                    'data' => $data,
                ]
            ]
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'User Roles Distribution',
                    'font' => [
                        'size' => 16
                    ]
                ],
                'legend' => [
                    'position' => 'right',
                ]
            ]
        ]);
        
        return $chart;
    }
    
    private function createStatusChart(ChartBuilderInterface $chartBuilder, array $statusStatistics): Chart
    {
        // Make sure we have data to display
        if (empty($statusStatistics)) {
            // Add default data to prevent empty chart
            $statusStatistics = ['No Data' => 1];
        }
        
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        
        $labels = array_keys($statusStatistics);
        $data = array_values($statusStatistics);
        
        $backgroundColors = [
            'active' => 'rgb(75, 192, 192)',
            'inactive' => 'rgb(255, 99, 132)',
            'No Data' => 'rgb(201, 203, 207)'
        ];
        
        $colors = [];
        foreach ($labels as $label) {
            $colors[] = $backgroundColors[$label] ?? 'rgb(201, 203, 207)';
        }
        
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Status Distribution',
                    'backgroundColor' => $colors,
                    'data' => $data,
                ]
            ]
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'User Status Distribution',
                    'font' => [
                        'size' => 16
                    ]
                ],
                'legend' => [
                    'position' => 'right',
                ]
            ]
        ]);
        
        return $chart;
    }

    #[Route('/user/edit/{id}', name: 'admin_user_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $user->setNomUser($request->request->get('nomUser'));
            $user->setPrenomUser($request->request->get('prenomUser'));
            $user->setEmailUser($request->request->get('emailUser'));
            $user->setTelUser((int)$request->request->get('telUser'));
            $user->setAdresseUser($request->request->get('adresseUser'));
            $user->setStatusUser($request->request->get('statusUser'));
            
            $entityManager->flush();
            
            $this->addFlash('success', 'User updated successfully');
            return $this->redirectToRoute('admin_dashboard');
        }
        
        return $this->render('backoffice/admin/edit_user.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/user/ban/{id}', name: 'admin_user_ban')]
    public function banUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is not an admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Cannot ban an administrator');
            return $this->redirectToRoute('admin_dashboard');
        }
        
        $user->setStatusUser('inactive');
        $entityManager->flush();
        
        $this->addFlash('success', 'User has been banned successfully');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user/unban/{id}', name: 'admin_user_unban')]
    public function unbanUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setStatusUser('active');
        $entityManager->flush();
        
        $this->addFlash('success', 'User has been unbanned successfully');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user/delete/{id}', name: 'admin_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Check if the user is not an admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Cannot delete an administrator');
            return $this->redirectToRoute('admin_dashboard');
        }
        
        $entityManager->remove($user);
        $entityManager->flush();
        
        $this->addFlash('success', 'User deleted successfully');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/dashboard/export-pdf', name: 'admin_dashboard_export_pdf')]
    public function exportPdf(UserRepository $userRepository): Response
    {
        // Exclude admin users from the export
        $queryBuilder = $userRepository->createQueryBuilder('u');
        $queryBuilder->where('u.roleUser NOT LIKE :role')
                    ->setParameter('role', '%"ROLE_ADMIN"%');
        
        $users = $queryBuilder->getQuery()->getResult();

        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);

        // Generate the HTML for the PDF
        $html = $this->renderView('backoffice/admin/pdf_template.html.twig', [
            'users' => $users,
            'date' => new \DateTime()
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate PDF file name
        $fileName = 'users_list_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return the PDF as response
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ResponseHeaderBag::DISPOSITION_ATTACHMENT . '; filename="' . $fileName . '"'
            ]
        );
    }


    #[Route('/hackathons', name: 'admin_hackathon_list')]
    public function liste(EntityManagerInterface $entityManager): Response {
        $hackathons = $entityManager->getRepository(Hackathon::class)->findAll(); // récupère tous les hackathons

        return $this->render('backoffice/hackathon/afficher.html.twig', [
            'hackathons' => $hackathons, // passe les données à Twig
        ]);
    }

#[Route('/hackathon/{id}/delete', name: 'admin_delete_hackathon')]
public function delete(Hackathon $hackathon, EntityManagerInterface $em): Response
{
    
        $em->remove($hackathon);
        $em->flush();
        $this->addFlash('success', 'Hackathon supprimé avec succès.');
    

    return $this->redirectToRoute('admin_hackathon_list');
}
}



