<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Article;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $articles = Article::all();
        $users = User::all();

        if ($articles->isEmpty() || $users->isEmpty()) {
            return;
        }

        $comments = [
            [
                'content' => 'Excellent article ! Très bien expliqué et très utile pour comprendre les nouvelles fonctionnalités de React 18.',
                'article_id' => 1,
                'user_id' => 2
            ],
            [
                'content' => 'Merci pour ce guide complet. J\'ai pu implémenter le rendu concurrent dans mon projet grâce à vos explications.',
                'article_id' => 1,
                'user_id' => 3
            ],
            [
                'content' => 'Très bon article sur Laravel ! Les bonnes pratiques mentionnées sont essentielles pour maintenir un code propre.',
                'article_id' => 2,
                'user_id' => 1
            ],
            [
                'content' => 'J\'utilise déjà certaines de ces pratiques, mais j\'ai appris de nouvelles techniques. Merci !',
                'article_id' => 2,
                'user_id' => 4
            ],
            [
                'content' => 'TypeScript a vraiment changé ma façon de développer. Cet article résume parfaitement les avantages et inconvénients.',
                'article_id' => 3,
                'user_id' => 5
            ],
            [
                'content' => 'Je suis en train de migrer mon projet vers TypeScript. Cet article m\'aide beaucoup dans ma décision.',
                'article_id' => 3,
                'user_id' => 6
            ],
            [
                'content' => 'Les techniques d\'optimisation mentionnées sont très pratiques. J\'ai déjà vu une amélioration des performances.',
                'article_id' => 4,
                'user_id' => 7
            ],
            [
                'content' => 'L\'optimisation des images est cruciale. Merci pour ces conseils sur les formats modernes.',
                'article_id' => 4,
                'user_id' => 8
            ],
            [
                'content' => 'L\'IA transforme vraiment le développement web. J\'aimerais en savoir plus sur TensorFlow.js.',
                'article_id' => 5,
                'user_id' => 1
            ],
            [
                'content' => 'Excellent article sur l\'IA ! J\'ai commencé à expérimenter avec des chatbots simples.',
                'article_id' => 5,
                'user_id' => 2
            ],
            [
                'content' => 'La sécurité web est un sujet crucial. Merci pour ce guide complet sur les bonnes pratiques.',
                'article_id' => 6,
                'user_id' => 3
            ],
            [
                'content' => 'J\'ai implémenté plusieurs de ces mesures de sécurité dans mon projet. Très utile !',
                'article_id' => 6,
                'user_id' => 4
            ],
            [
                'content' => 'React 18 est vraiment impressionnant. Le rendu concurrent améliore considérablement l\'expérience utilisateur.',
                'article_id' => 1,
                'user_id' => 5
            ],
            [
                'content' => 'Laravel reste mon framework préféré. Ces bonnes pratiques m\'aident à écrire un code plus maintenable.',
                'article_id' => 2,
                'user_id' => 6
            ],
            [
                'content' => 'TypeScript m\'a fait gagner beaucoup de temps en évitant les erreurs de type. Je recommande !',
                'article_id' => 3,
                'user_id' => 7
            ],
            [
                'content' => 'L\'optimisation des performances est un processus continu. Merci pour ces techniques avancées.',
                'article_id' => 4,
                'user_id' => 8
            ],
            [
                'content' => 'L\'IA ouvre de nouvelles possibilités fascinantes. J\'ai hâte de voir ce que l\'avenir nous réserve.',
                'article_id' => 5,
                'user_id' => 1
            ],
            [
                'content' => 'La sécurité ne doit jamais être négligée. Cet article rappelle l\'importance des bonnes pratiques.',
                'article_id' => 6,
                'user_id' => 2
            ]
        ];

        foreach ($comments as $commentData) {
            // Vérifier que l'article et l'utilisateur existent
            $article = $articles->find($commentData['article_id']);
            $user = $users->find($commentData['user_id']);
            
            if ($article && $user) {
                Comment::create([
                    'content' => $commentData['content'],
                    'article_id' => $commentData['article_id'],
                    'user_id' => $commentData['user_id'],
                    'is_approved' => true,
                    'created_at' => now()->subDays(rand(1, 10))->subHours(rand(1, 23)),
                ]);
            }
        }

        // Créer quelques réponses aux commentaires
        $parentComments = Comment::whereNull('parent_id')->get();
        
        foreach ($parentComments->take(5) as $parentComment) {
            $randomUser = $users->where('id', '!=', $parentComment->user_id)->random();
            
            Comment::create([
                'content' => 'Merci pour votre commentaire ! Je suis ravi que l\'article vous ait été utile.',
                'article_id' => $parentComment->article_id,
                'user_id' => $parentComment->article->user_id, // L'auteur de l'article répond
                'parent_id' => $parentComment->id,
                'is_approved' => true,
                'created_at' => $parentComment->created_at->addHours(rand(1, 24)),
            ]);
        }
    }
}

