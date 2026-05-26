<?php

use App\Http\Controllers\Frontend\Page\HomepageController;
use App\Http\Controllers\Frontend\Common\LanguageController;
use App\Http\Controllers\Frontend\Student\DashboardController;
use App\Http\Controllers\Frontend\Page\HistoryOfGpniController;
use App\Http\Controllers\Frontend\Page\AdvisoryBoardExpertLectureController;
use App\Http\Controllers\Frontend\Page\FAQController;
use App\Http\Controllers\Frontend\Page\ISSNOfficialPartnerAffiliateController;
use App\Http\Controllers\Frontend\Page\OurPolicyController;
use App\Http\Controllers\Frontend\Page\ConferenceController;
use App\Http\Controllers\Frontend\Page\InsuranceProfessionalMembershipController;
use App\Http\Controllers\Frontend\Page\GiftCardController;
use App\Http\Controllers\Frontend\Page\ArticleController;
use App\Http\Controllers\Frontend\Page\CertificationCourseController;
use App\Http\Controllers\Frontend\Page\WhyWeAreDifferentController;
use App\Http\Controllers\Frontend\Page\MembershipController;
use App\Http\Controllers\Frontend\Page\ContactUsController;
use App\Http\Controllers\Frontend\Page\GlobalEducationPartnersController;
use App\Http\Controllers\Frontend\Page\NutritionistController;
use App\Http\Controllers\Frontend\Student\MyStorageController;
use App\Http\Controllers\Frontend\Student\CourseController;
use App\Http\Controllers\Frontend\Student\AskQuestionController;
use App\Http\Controllers\Frontend\Page\TvController;
use App\Http\Controllers\Frontend\Page\MasterClassController;
use App\Http\Controllers\Frontend\Student\ProfileController;
use App\Http\Controllers\Frontend\Student\BuyStudyMaterialController;
use App\Http\Controllers\Frontend\Student\MemberCornerController;
use App\Http\Controllers\Frontend\Student\MyOrderController;
use App\Http\Controllers\Frontend\Student\QualificationController;
use App\Http\Controllers\Frontend\Student\ReferFriendController;
use App\Http\Controllers\Frontend\Student\CartController;
use App\Http\Controllers\Frontend\Page\ProductController;
use App\Http\Controllers\Frontend\Student\FinalExamController;
use App\Http\Controllers\Frontend\Student\ModuleExamController;
use App\Http\Controllers\Frontend\Student\TechnicalSupportController;
use App\Http\Controllers\Frontend\Common\SubscriptionController;
use App\Http\Controllers\Frontend\Page\ArticleCategoryController;
use App\Http\Controllers\Frontend\Student\WalletController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/frontend-auth.php';

Route::post('set-language', [LanguageController::class, 'setLanguage'])->name('set-language');

Route::middleware(['set_language'])->group(function () {
    // Page routes
        Route::get('/', [HomepageController::class, 'index'])->name('homepage');
        Route::get('why-we-are-different', [WhyWeAreDifferentController::class, 'index'])->name('why-we-are-different');
        Route::get('advisory-board-and-expert-lectures', [AdvisoryBoardExpertLectureController::class, 'index'])->name('advisory-board-and-expert-lectures');
        Route::get('faqs', [FAQController::class, 'index'])->name('faqs');
        Route::get('history-of-gpni', [HistoryOfGpniController::class, 'index'])->name('history-of-gpni');
        Route::get('our-policies', [OurPolicyController::class, 'index'])->name('our-policies');
        Route::get('insurance-and-professional-membership', [InsuranceProfessionalMembershipController::class, 'index'])->name('insurance-and-professional-membership');
        Route::get('global-education-partners', [GlobalEducationPartnersController::class, 'index'])->name('global-education-partners');
        Route::get('issn-official-partners-and-affiliates', [ISSNOfficialPartnerAffiliateController::class, 'index'])->name('issn-official-partners-and-affiliates');

        Route::prefix('memberships')->name('memberships.')->group(function() {
            Route::get('/', [MembershipController::class, 'index'])->name('index');
        });

        Route::prefix('article-categories')->name('article-categories.')->group(function() {
            Route::get('/{article_category}/{title}', [ArticleCategoryController::class, 'index'])->name('index');
        });
        Route::prefix('articles')->name('articles.')->group(function() {
            Route::get('/', [ArticleController::class, 'index'])->name('index');
            Route::get('show/{article}/{title}', [ArticleController::class, 'show'])->name('show');
        });
        Route::prefix('conferences')->name('conferences.')->group(function() {
            Route::get('/', [ConferenceController::class, 'index'])->name('index');
            Route::get('show/{conference}/{title}', [ConferenceController::class, 'show'])->name('show');
        });
        Route::prefix('contact-us')->name('contact-us.')->group(function() {
            Route::get('/', [ContactUsController::class, 'index'])->name('index');
            Route::post('/', [ContactUsController::class, 'store'])->name('store');
        });
        Route::prefix('gift-cards')->name('gift-cards.')->group(function() {
            Route::get('/', [GiftCardController::class, 'index'])->name('index');
            Route::post('checkout', [GiftCardController::class, 'checkout'])->name('checkout');
            Route::get('success', [GiftCardController::class, 'success'])->name('success');
        });
        Route::prefix('gpni-tv')->name('gpni-tv.')->group(function() {
            Route::get('/', [TvController::class, 'index'])->name('index');
            Route::get('show/{course}/{title}', [TvController::class, 'show'])->name('show');
            Route::post('checkout', [TvController::class, 'checkout'])->name('checkout');
            Route::get('success', [TvController::class, 'success'])->name('success');
        });
        Route::prefix('nutritionists')->name('nutritionists.')->group(function() {
            Route::get('/', [NutritionistController::class, 'index'])->name('index');
            Route::post('contact', [NutritionistController::class, 'contact'])->name('contact');
            Route::get('fetch/{nutritionist}', [NutritionistController::class, 'fetch'])->name('fetch');
        });
        Route::prefix('master-classes')->name('master-classes.')->group(function() {
            Route::get('/', [MasterClassController::class, 'index'])->name('index');
            Route::get('show/{course}/{title}', [MasterClassController::class, 'show'])->name('show');
        });
        Route::prefix('products')->name('products.')->group(function() {
            Route::get('/', [ProductController::class, 'index'])->name('index');
        });
        Route::prefix('certification-courses')->name('certification-courses.')->group(function() {
            Route::get('show/{course}/{title}', [CertificationCourseController::class, 'show'])->name('show');
            Route::get('purchase/{course}', [CertificationCourseController::class, 'purchase'])->name('purchase');
        });
    // Page routes


    // Subscription route
        Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription');
    // Subscription route


    // Student routes
        Route::middleware(['auth', 'role:student'])->group(function () {
            // All payment routes
                Route::prefix('master-classes')->name('master-classes.')->group(function() {
                    Route::post('checkout', [MasterClassController::class, 'checkout'])->name('checkout');
                    Route::get('success', [MasterClassController::class, 'success'])->name('success');
                });

                Route::prefix('certification-courses')->name('certification-courses.')->group(function() {
                    Route::post('checkout', [CertificationCourseController::class, 'checkout'])->name('checkout');
                    Route::get('success', [CertificationCourseController::class, 'success'])->name('success');
                });

                Route::prefix('products')->name('products.')->group(function() {
                    Route::post('checkout', [ProductController::class, 'checkout'])->name('checkout');
                    Route::get('success', [ProductController::class, 'success'])->name('success');
                });

                Route::prefix('buy-study-materials')->name('buy-study-materials.')->group(function() {
                    Route::post('checkout', [BuyStudyMaterialController::class, 'checkout'])->name('checkout');
                    Route::get('success', [BuyStudyMaterialController::class, 'success'])->name('success');
                });

                Route::prefix('memberships')->name('memberships.')->group(function() {
                    Route::post('checkout', [MembershipController::class, 'checkout'])->name('checkout');
                    Route::get('success', [MembershipController::class, 'success'])->name('success');
                });

                Route::prefix('final-exam')->name('final-exam.')->group(function() {
                    Route::post('checkout', [CourseController::class, 'checkout'])->name('checkout');
                    Route::get('success', [CourseController::class, 'success'])->name('success');
                });
            // All payment routes

            Route::prefix('dashboard')->name('dashboard.')->group(function() {
                Route::get('/', [DashboardController::class, 'index'])->name('index');
            });

            Route::prefix('ask-questions')->name('ask-questions.')->group(function() {
                Route::get('/', [AskQuestionController::class, 'index'])->name('index');
                Route::post('/', [AskQuestionController::class, 'store'])->name('store');
                Route::get('histories', [AskQuestionController::class, 'histories'])->name('histories');
                Route::get('histories/{ask_question}', [AskQuestionController::class, 'show'])->name('show');
                Route::post('histories/update/{ask_question}', [AskQuestionController::class, 'update'])->name('update');
            });

            Route::prefix('courses')->name('courses.')->group(function() {
                Route::get('/', [CourseController::class, 'index'])->name('index');
                Route::get('/gpni-tv', [CourseController::class, 'index'])->name('gpni-tv');
                Route::get('{course}', [CourseController::class, 'show'])->name('show');
                Route::get('{course}/{course_module}/{course_chapter}', [CourseController::class, 'showMore'])->name('show-more');
                Route::post('get-file', [CourseController::class, 'getFile'])->name('get-file');
            });

            Route::prefix('carts')->name('carts.')->group(function() {
                Route::get('/', [CartController::class, 'index'])->name('index');
                Route::post('/', [CartController::class, 'store'])->name('store');
                Route::post('update-quantity', [CartController::class, 'updateQuantity'])->name('update-quantity');
                Route::post('delete-item', [CartController::class, 'destroy'])->name('destroy');
            });

            Route::get('my-orders', [MyOrderController::class, 'index'])->name('my-orders');
            
            Route::get('my-storage', [MyStorageController::class, 'index'])->name('my-storage');

            Route::prefix('refer-friends')->name('refer-friends.')->group(function() {
                Route::get('/', [ReferFriendController::class, 'index'])->name('index');
                Route::post('/', [ReferFriendController::class, 'store'])->name('store');
                Route::post('withdraw', [ReferFriendController::class, 'withdraw'])->name('withdraw');
            });

            Route::prefix('profile')->name('profile.')->group(function() {
                Route::get('/', [ProfileController::class, 'index'])->name('index');
                Route::post('/', [ProfileController::class, 'update'])->name('update');
            });

            Route::get('buy-study-materials', [BuyStudyMaterialController::class, 'index'])->name('buy-study-materials');

            Route::get('member-corner', [MemberCornerController::class, 'index'])->name('member-corner');
            
            Route::prefix('module-exams')->name('module-exams.')->group(function() {
                Route::get('{course}/{course_module}', [ModuleExamController::class, 'index'])->name('index');
                Route::post('{course}/{course_module}', [ModuleExamController::class, 'store'])->name('store');
                Route::get('{course}/{course_module}/{course_module_exam}/results', [ModuleExamController::class, 'results'])->name('results');
            });

            Route::prefix('final-exam')->name('final-exam.')->group(function() {
                Route::get('{course}', [FinalExamController::class, 'index'])->name('index');
                Route::post('{course}', [FinalExamController::class, 'store'])->name('store');
                Route::get('{course}/{course_final_exam}/results', [FinalExamController::class, 'results'])->name('results');
            });

            Route::get('qualifications', [QualificationController::class, 'index'])->name('qualifications');
            Route::post('cec', [QualificationController::class, 'cecStore'])->name('cec.store');

            Route::prefix('technical-supports')->name('technical-supports.')->group(function() {
                Route::get('/', [TechnicalSupportController::class, 'index'])->name('index');
                Route::post('/', [TechnicalSupportController::class, 'store'])->name('store');
                Route::get('histories', [TechnicalSupportController::class, 'histories'])->name('histories');
                Route::get('histories/{technical_support}', [TechnicalSupportController::class, 'show'])->name('show');
                Route::post('histories/update/{technical_support}', [TechnicalSupportController::class, 'update'])->name('update');
            });

            Route::prefix('wallet')->name('wallet.')->group(function() {
                Route::get('/', [WalletController::class, 'index'])->name('index');
            });
        });
    // Student routes 
});