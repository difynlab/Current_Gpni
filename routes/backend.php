<?php

use App\Http\Controllers\Backend\Administration\CKEditorController;
use App\Http\Controllers\Backend\Administration\DashboardController;
use App\Http\Controllers\Backend\Administration\MyProfileController;
use App\Http\Controllers\Backend\Administration\SettingsController;
use App\Http\Controllers\Backend\Article\ArticleCategoryController;
use App\Http\Controllers\Backend\Article\ArticleController;
use App\Http\Controllers\Backend\Communication\AskQuestionController;
use App\Http\Controllers\Backend\Communication\ConnectionController;
use App\Http\Controllers\Backend\Communication\ContactCoachController;
use App\Http\Controllers\Backend\Communication\ReferFriendController;
use App\Http\Controllers\Backend\Communication\SubscriptionController;
use App\Http\Controllers\Backend\Communication\TechnicalSupportController;
use App\Http\Controllers\Backend\Conference\ConferenceController;
use App\Http\Controllers\Backend\Course\CourseChapterController;
use App\Http\Controllers\Backend\Course\CourseController;
use App\Http\Controllers\Backend\Course\CourseFinalExamQuestionController;
use App\Http\Controllers\Backend\Course\CourseInformationController;
use App\Http\Controllers\Backend\Course\CourseModuleController;
use App\Http\Controllers\Backend\Course\CourseModuleExamQuestionController;
use App\Http\Controllers\Backend\Course\CourseReviewController;
use App\Http\Controllers\Backend\Course\UserCourseController;
use App\Http\Controllers\Backend\FAQ\FAQController;
use App\Http\Controllers\Backend\Media\MediaController;
use App\Http\Controllers\Backend\Page\AdvisoryBoardExpertLectureController;
use App\Http\Controllers\Backend\Page\ArticleController as PageArticleController;
use App\Http\Controllers\Backend\Page\AuthenticationController;
use App\Http\Controllers\Backend\Page\CartController;
use App\Http\Controllers\Backend\Page\CertificationCourseController;
use App\Http\Controllers\Backend\Page\CommonController;
use App\Http\Controllers\Backend\Page\ConferenceController as PageConferenceController;
use App\Http\Controllers\Backend\Page\ContactUsController;
use App\Http\Controllers\Backend\Page\FAQController as PageFAQController;
use App\Http\Controllers\Backend\Page\GiftCardController;
use App\Http\Controllers\Backend\Page\GlobalEducationPartnerController as PageGlobalEducationPartnerController;
use App\Http\Controllers\Backend\Testimonial\TestimonialController;
use App\Http\Controllers\Backend\Page\HistoryOfGpniController;
use App\Http\Controllers\Backend\Page\HomepageController;
use App\Http\Controllers\Backend\Page\InsuranceProfessionalMembershipController;
use App\Http\Controllers\Backend\Page\ISSNOfficialPartnerAffiliateController;
use App\Http\Controllers\Backend\Page\MasterClassController;
use App\Http\Controllers\Backend\Page\MembershipController;
use App\Http\Controllers\Backend\Page\NutritionistController;
use App\Http\Controllers\Backend\Page\PageController;
use App\Http\Controllers\Backend\Page\OurPolicyController;
use App\Http\Controllers\Backend\Page\ProductController as PageProductController;
use App\Http\Controllers\Backend\Page\StudentDashboardController;
use App\Http\Controllers\Backend\Page\TvController;
use App\Http\Controllers\Backend\Page\WhyWeAreDifferentController;
use App\Http\Controllers\Backend\Person\AdminController;
use App\Http\Controllers\Backend\Person\AdvisoryBoardController as PersonAdvisoryBoardController;
use App\Http\Controllers\Backend\Person\GlobalEducationPartnerController;
use App\Http\Controllers\Backend\Person\ISSNPartnerController;
use App\Http\Controllers\Backend\Person\OurFounderController;
use App\Http\Controllers\Backend\Person\UserController;
use App\Http\Controllers\Backend\Policy\PolicyCategoryController;
use App\Http\Controllers\Backend\Policy\PolicyController;
use App\Http\Controllers\Backend\Product\ProductCategoryController;
use App\Http\Controllers\Backend\Product\ProductController;
use App\Http\Controllers\Backend\Coupon\CouponController;
use App\Http\Controllers\Backend\Purchase\CoursePurchaseController;
use App\Http\Controllers\Backend\Purchase\FinalExamPurchaseController;
use App\Http\Controllers\Backend\Purchase\GiftCardPurchaseController;
use App\Http\Controllers\Backend\Purchase\MaterialPurchaseController;
use App\Http\Controllers\Backend\Purchase\MembershipPurchaseController;
use App\Http\Controllers\Backend\Purchase\ProductPurchaseController;
use App\Http\Controllers\Backend\Result\ExamResultController;
use App\Http\Controllers\Backend\Webinar\WebinarController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/backend-auth.php';

Route::get('/admin', function () {
    return redirect()->route('backend.login');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::resource('dashboard', DashboardController::class)->only('index');

    // CkEditor upload route
        Route::post('ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');
    // CkEditor upload route


    // All page related routes
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::prefix('pages')->name('pages.')->group(function() {
            Route::get('homepage/{language}', [HomepageController::class, 'index'])->name('homepage.index');
            Route::post('homepage/{language}', [HomepageController::class, 'update'])->name('homepage.update');

            Route::get('why-we-are-different/{language}', [WhyWeAreDifferentController::class, 'index'])->name('why-we-are-different.index');
            Route::post('why-we-are-different/{language}', [WhyWeAreDifferentController::class, 'update'])->name('why-we-are-different.update');

            Route::get('history-of-gpni/{language}', [HistoryOfGpniController::class, 'index'])->name('history-of-gpni.index');
            Route::post('history-of-gpni/{language}', [HistoryOfGpniController::class, 'update'])->name('history-of-gpni.update');

            Route::get('gift-card/{language}', [GiftCardController::class, 'index'])->name('gift-card.index');
            Route::post('gift-card/{language}', [GiftCardController::class, 'update'])->name('gift-card.update');

            Route::get('advisory-board-and-expert-lectures/{language}', [AdvisoryBoardExpertLectureController::class, 'index'])->name('advisory-board-and-expert-lectures.index');
            Route::post('advisory-board-and-expert-lectures/{language}', [AdvisoryBoardExpertLectureController::class, 'update'])->name('advisory-board-and-expert-lectures.update');

            Route::get('issn-official-partners-and-affiliates/{language}', [ISSNOfficialPartnerAffiliateController::class, 'index'])->name('issn-official-partners-and-affiliates.index');
            Route::post('issn-official-partners-and-affiliates/{language}', [ISSNOfficialPartnerAffiliateController::class, 'update'])->name('issn-official-partners-and-affiliates.update');

            Route::get('faq/{language}', [PageFAQController::class, 'index'])->name('faq.index');
            Route::post('faq/{language}', [PageFAQController::class, 'update'])->name('faq.update');

            Route::get('our-policies/{language}', [OurPolicyController::class, 'index'])->name('our-policies.index');
            Route::post('our-policies/{language}', [OurPolicyController::class, 'update'])->name('our-policies.update');

            Route::get('insurance-professional-membership/{language}', [InsuranceProfessionalMembershipController::class, 'index'])->name('insurance-professional-membership.index');
            Route::post('insurance-professional-membership/{language}', [InsuranceProfessionalMembershipController::class, 'update'])->name('insurance-professional-membership.update');

            Route::get('article/{language}', [PageArticleController::class, 'index'])->name('article.index');
            Route::post('article/{language}', [PageArticleController::class, 'update'])->name('article.update');

            Route::get('conference/{language}', [PageConferenceController::class, 'index'])->name('conference.index');
            Route::post('conference/{language}', [PageConferenceController::class, 'update'])->name('conference.update');

            Route::get('membership/{language}', [MembershipController::class, 'index'])->name('membership.index');
            Route::post('membership/{language}', [MembershipController::class, 'update'])->name('membership.update');

            Route::get('contact-us/{language}', [ContactUsController::class, 'index'])->name('contact-us.index');
            Route::post('contact-us/{language}', [ContactUsController::class, 'update'])->name('contact-us.update');

            Route::get('nutritionist/{language}', [NutritionistController::class, 'index'])->name('nutritionist.index');
            Route::post('nutritionist/{language}', [NutritionistController::class, 'update'])->name('nutritionist.update');

            Route::get('global-education-partner/{language}', [PageGlobalEducationPartnerController::class, 'index'])->name('global-education-partner.index');
            Route::post('global-education-partner/{language}', [PageGlobalEducationPartnerController::class, 'update'])->name('global-education-partner.update');

            Route::get('tv/{language}', [TvController::class, 'index'])->name('tv.index');
            Route::post('tv/{language}', [TvController::class, 'update'])->name('tv.update');

            Route::get('master-class/{language}', [MasterClassController::class, 'index'])->name('master-class.index');
            Route::post('master-class/{language}', [MasterClassController::class, 'update'])->name('master-class.update');

            Route::get('certification-course/{language}', [CertificationCourseController::class, 'index'])->name('certification-course.index');
            Route::post('certification-course/{language}', [CertificationCourseController::class, 'update'])->name('certification-course.update');

            Route::get('product/{language}', [PageProductController::class, 'index'])->name('product.index');
            Route::post('product/{language}', [PageProductController::class, 'update'])->name('product.update');

            Route::get('cart/{language}', [CartController::class, 'index'])->name('cart.index');
            Route::post('cart/{language}', [CartController::class, 'update'])->name('cart.update');

            Route::get('student-dashboard/{language}', [StudentDashboardController::class, 'index'])->name('student-dashboard.index');
            Route::post('student-dashboard/{language}', [StudentDashboardController::class, 'update'])->name('student-dashboard.update');

            Route::get('authentication/{language}', [AuthenticationController::class, 'index'])->name('authentication.index');
            Route::post('authentication/{language}', [AuthenticationController::class, 'update'])->name('authentication.update');

            Route::get('common/{language}', [CommonController::class, 'index'])->name('common.index');
            Route::post('common/{language}', [CommonController::class, 'update'])->name('common.update');
        });
    // All page related routes


    // All administrations routes
        Route::resource('my-profile', MyProfileController::class)->only('index', 'update');
        Route::resource('settings', SettingsController::class)->only('index', 'update');
    // All administrations routes


    // All course related routes
        Route::resource('courses', CourseController::class)->except('show');
        Route::get('courses/filter', [CourseController::class, 'filter'])->name('courses.filter');

        Route::prefix('courses')->name('courses.')->group(function() {
            Route::get('information/{course}', [CourseInformationController::class, 'index'])->name('information.index');
            Route::post('information/{course}', [CourseInformationController::class, 'update'])->name('information.update');

            Route::get('reviews/{course}', [CourseReviewController::class, 'index'])->name('reviews.index');
            Route::get('reviews/{course}/create', [CourseReviewController::class, 'create'])->name('reviews.create');
            Route::post('reviews/{course}/store', [CourseReviewController::class, 'store'])->name('reviews.store');
            Route::get('reviews/edit/{course}/{course_review}', [CourseReviewController::class, 'edit'])->name('reviews.edit');
            Route::post('reviews/update/{course}/{course_review}', [CourseReviewController::class, 'update'])->name('reviews.update');
            Route::delete('reviews/destroy/{course}/{course_review}', [CourseReviewController::class, 'destroy'])->name('reviews.destroy');

            Route::get('modules/{course}', [CourseModuleController::class, 'index'])->name('modules.index');
            Route::post('modules/store', [CourseModuleController::class, 'store'])->name('modules.store');
            Route::get('modules/edit/{course_module}', [CourseModuleController::class, 'edit'])->name('modules.edit');
            Route::post('modules/update/{course_module}', [CourseModuleController::class, 'update'])->name('modules.update');
            Route::delete('modules/destroy/{course_module}', [CourseModuleController::class, 'destroy'])->name('modules.destroy');

            Route::get('modules/chapters/{course_module}', [CourseChapterController::class, 'index'])->name('module-chapters.index');
            Route::get('modules/chapters/{course_module}/create', [CourseChapterController::class, 'create'])->name('module-chapters.create');
            Route::post('modules/chapters/{course_module}/store', [CourseChapterController::class, 'store'])->name('module-chapters.store');
            Route::get('modules/chapters/{course_module}/edit/{course_chapter}', [CourseChapterController::class, 'edit'])->name('module-chapters.edit');
            Route::post('modules/chapters/{course_module}/update/{course_chapter}', [CourseChapterController::class, 'update'])->name('module-chapters.update');
            Route::delete('modules/chapters/{course_module}/destroy/{course_chapter}', [CourseChapterController::class, 'destroy'])->name('module-chapters.destroy');

            Route::get('modules/exam-questions/{course_module}', [CourseModuleExamQuestionController::class, 'index'])->name('module-exam-questions.index');
            Route::get('modules/exam-questions/{course_module}/create', [CourseModuleExamQuestionController::class, 'create'])->name('module-exam-questions.create');
            Route::post('modules/exam-questions/{course_module}/store', [CourseModuleExamQuestionController::class, 'store'])->name('module-exam-questions.store');
            Route::get('modules/exam-questions/{course_module}/edit/{course_module_exam_question}', [CourseModuleExamQuestionController::class, 'edit'])->name('module-exam-questions.edit');
            Route::post('modules/exam-questions/{course_module}/update/{course_module_exam_question}', [CourseModuleExamQuestionController::class, 'update'])->name('module-exam-questions.update');
            Route::delete('modules/exam-questions/{course_module}/destroy/{course_module_exam_question}', [CourseModuleExamQuestionController::class, 'destroy'])->name('module-exam-questions.destroy');

            Route::get('final-exam-questions/{course}', [CourseFinalExamQuestionController::class, 'index'])->name('final-exam-questions.index');
            Route::get('final-exam-questions/{course}/create', [CourseFinalExamQuestionController::class, 'create'])->name('final-exam-questions.create');
            Route::post('final-exam-questions/{course}/store', [CourseFinalExamQuestionController::class, 'store'])->name('final-exam-questions.store');
            Route::get('final-exam-questions/{course}/edit/{course_final_exam_question}', [CourseFinalExamQuestionController::class, 'edit'])->name('final-exam-questions.edit');
            Route::post('final-exam-questions/{course}/update/{course_final_exam_question}', [CourseFinalExamQuestionController::class, 'update'])->name('final-exam-questions.update');
            Route::delete('final-exam-questions/{course}/destroy/{course_final_exam_question}', [CourseFinalExamQuestionController::class, 'destroy'])->name('final-exam-questions.destroy');
        });
    // All course related routes


    // Coupons routes
        Route::resource('coupons', CouponController::class)->except('show');
        Route::get('coupons/filter', [CouponController::class, 'filter'])->name('coupons.filter');
    // Coupons routes


    // All article related routes
        Route::resource('article-categories', ArticleCategoryController::class)->except(['show']);
        Route::get('article-categories/filter', [ArticleCategoryController::class, 'filter'])->name('article-categories.filter');

        Route::resource('articles', ArticleController::class)->except(['show']);
        Route::get('articles/filter', [ArticleController::class, 'filter'])->name('articles.filter');
    // All article related routes


    // Conferences routes
        Route::resource('conferences', ConferenceController::class)->except('show');
        Route::get('conferences/filter', [ConferenceController::class, 'filter'])->name('conferences.filter');
    // Conferences routes


    // FAQs routes
        Route::resource('faqs', FAQController::class)->except('show');
        Route::get('faqs/filter', [FAQController::class, 'filter'])->name('faqs.filter');
    // FAQs routes


    // Testimonials routes
        Route::resource('testimonials', TestimonialController::class)->except('show');
        Route::get('testimonials/filter', [TestimonialController::class, 'filter'])->name('testimonials.filter');
    // Testimonials routes


    // All product related routes
        Route::resource('product-categories', ProductCategoryController::class)->except(['show']);
        Route::get('product-categories/filter', [ProductCategoryController::class, 'filter'])->name('product-categories.filter');

        Route::resource('products', ProductController::class)->except('show');
        Route::get('products/filter', [ProductController::class, 'filter'])->name('products.filter');
    // All product related routes


    // Medias routes
        Route::resource('medias', MediaController::class)->except('show');
        Route::get('medias/filter', [MediaController::class, 'filter'])->name('medias.filter');
    // Medias routes


    // All users routes
        Route::prefix('persons')->name('persons.')->group(function() {
            // Users routes
                Route::resource('users', UserController::class)->except(['show']);
                Route::get('users/filter', [UserController::class, 'filter'])->name('users.filter');

                Route::prefix('users')->name('users.')->group(function() {
                    Route::prefix('{user}/courses')->name('courses.')->group(function() {
                        // User courses routes
                            Route::get('/', [UserCourseController::class, 'index'])->name('index');
                            Route::get('create', [UserCourseController::class, 'create'])->name('create');
                            Route::post('/', [UserCourseController::class, 'store'])->name('store');
                            Route::get('{course_purchase}/edit', [UserCourseController::class, 'edit'])->name('edit');
                            Route::get('filter', [UserCourseController::class, 'filter'])->name('filter');
                            Route::post('{course_purchase}', [UserCourseController::class, 'update'])->name('update');
                            Route::delete('{course_purchase}', [UserCourseController::class, 'destroy'])->name('destroy');
                        // User courses routes
                    });
                });

                Route::prefix('users')->name('users.')->group(function() {
                    Route::prefix('{user}/points')->name('points.')->group(function() {
                        // User points routes
                            Route::get('/', [UserController::class, 'points'])->name('index');
                        // User points routes
                    });

                    Route::prefix('/cec-points')->name('cec-points.')->group(function() {
                        // CEC points routes
                            Route::get('/', [UserController::class, 'cecPointsCommon'])->name('common');
                        // CEC points routes
                    });

                    Route::prefix('{user}/cec-points')->name('cec-points.')->group(function() {
                        // User CEC points routes
                            Route::get('/', [UserController::class, 'cecPoints'])->name('index');
                            Route::post('/', [UserController::class, 'cecPointsStore'])->name('store');
                            Route::post('{cec_point_activity}', [UserController::class, 'cecPointsUpdate'])->name('update');
                        // User CEC points routes
                    });
                });
            // Users routes

            // Admins routes
                Route::resource('admins', AdminController::class)->except(['show']);
                Route::get('admins/filter', [AdminController::class, 'filter'])->name('admins.filter');
            // Admins routes

            // Advisory board routes
                Route::resource('advisory-boards', PersonAdvisoryBoardController::class)->except('show');
                Route::get('advisory-boards/filter', [PersonAdvisoryBoardController::class, 'filter'])->name('advisory-boards.filter');
            // Advisory board routes

            // Advisory board routes
                Route::resource('our-founders', OurFounderController::class)->except('show');
                Route::get('our-founders/filter', [OurFounderController::class, 'filter'])->name('our-founders.filter');
            // Advisory board routes

            // ISSN partner routes
                Route::resource('issn-partners', ISSNPartnerController::class)->except('show');
                Route::get('issn-partners/filter', [ISSNPartnerController::class, 'filter'])->name('issn-partners.filter');
            // ISSN partner routes

            // Global education partners routes
                Route::resource('global-education-partners', GlobalEducationPartnerController::class)->except('show');
                Route::get('global-education-partners/filter', [GlobalEducationPartnerController::class, 'filter'])->name('global-education-partners.filter');
            // Global education partners routes
        });
    // All users routes


    // All communication routes
        Route::prefix('communications')->name('communications.')->group(function() {
            Route::prefix('contact-coaches')->name('contact-coaches.')->group(function() {
                Route::get('/', [ContactCoachController::class, 'index'])->name('index');
                Route::get('filter', [ContactCoachController::class, 'filter'])->name('filter');
                Route::delete('{contact_coach}', [ContactCoachController::class, 'destroy'])->name('destroy');
            });

            Route::resource('ask-questions', AskQuestionController::class)->except(['create', 'show']);
            Route::get('ask-questions/filter', [AskQuestionController::class, 'filter'])->name('ask-questions.filter');

            Route::resource('connections', ConnectionController::class)->except(['create', 'show']);
            
            Route::resource('refer-friends', ReferFriendController::class)->only(['index', 'destroy']);

            Route::prefix('technical-supports')->name('technical-supports.')->group(function() {
                Route::get('/', [TechnicalSupportController::class, 'index'])->name('index');
                Route::get('filter', [TechnicalSupportController::class, 'filter'])->name('filter');
                Route::get('{technical_support}/edit', [TechnicalSupportController::class, 'edit'])->name('edit');
                Route::put('{technical_support}', [TechnicalSupportController::class, 'update'])->name('update');
                Route::delete('{technical_support}', [TechnicalSupportController::class, 'destroy'])->name('destroy');
            });

            Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'destroy']);
        });
    // All communication routes


    // All purchase routes
        Route::prefix('purchases')->name('purchases.')->group(function() {
            Route::prefix('gift-card-purchases')->name('gift-card-purchases.')->group(function() {
                Route::get('/', [GiftCardPurchaseController::class, 'index'])->name('index');
                Route::get('{gift_card_purchase}/show', [GiftCardPurchaseController::class, 'show'])->name('show');
                Route::get('filter', [GiftCardPurchaseController::class, 'filter'])->name('filter');
                Route::delete('{gift_card_purchase}', [GiftCardPurchaseController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('course-purchases')->name('course-purchases.')->group(function() {
                Route::get('/', [CoursePurchaseController::class, 'index'])->name('index');
                Route::get('{course_purchase}/show', [CoursePurchaseController::class, 'show'])->name('show');
                Route::get('filter', [CoursePurchaseController::class, 'filter'])->name('filter');
                Route::delete('{course_purchase}', [CoursePurchaseController::class, 'destroy'])->name('destroy');

                Route::prefix('certificates')->name('certificates.')->group(function() {
                    Route::get('{course_purchase}', [CoursePurchaseController::class, 'certificate'])->name('index');
                    Route::post('{course_certificate}/update', [CoursePurchaseController::class, 'certificateUpdate'])->name('update');
                });
            });

            Route::prefix('product-purchases')->name('product-purchases.')->group(function() {
                Route::get('/', [ProductPurchaseController::class, 'index'])->name('index');
                Route::get('{product_purchase}/show', [ProductPurchaseController::class, 'show'])->name('show');
                Route::delete('/{product_purchase}', [ProductPurchaseController::class, 'destroy'])->name('destroy');
                Route::get('filter', [ProductPurchaseController::class, 'filter'])->name('filter');

                Route::get('products/{product_purchase}', [ProductPurchaseController::class, 'products'])->name('products');
            });

            Route::prefix('material-purchases')->name('material-purchases.')->group(function() {
                Route::get('/', [MaterialPurchaseController::class, 'index'])->name('index');
                Route::get('{material_purchase}/show', [MaterialPurchaseController::class, 'show'])->name('show');
                Route::post('{material_purchase}/send', [MaterialPurchaseController::class, 'send'])->name('send');
                Route::get('filter', [MaterialPurchaseController::class, 'filter'])->name('filter');
                Route::delete('{material_purchase}', [MaterialPurchaseController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('membership-purchases')->name('membership-purchases.')->group(function() {
                Route::get('/', [MembershipPurchaseController::class, 'index'])->name('index');
                Route::get('{membership_purchase}/show', [MembershipPurchaseController::class, 'show'])->name('show');
                Route::get('filter', [MembershipPurchaseController::class, 'filter'])->name('filter');
                Route::delete('{membership_purchase}', [MembershipPurchaseController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('final-exam-purchases')->name('final-exam-purchases.')->group(function() {
                Route::get('/', [FinalExamPurchaseController::class, 'index'])->name('index');
                Route::get('{final_exam_purchase}/show', [FinalExamPurchaseController::class, 'show'])->name('show');
                Route::get('filter', [FinalExamPurchaseController::class, 'filter'])->name('filter');
                Route::delete('{final_exam_purchase}', [FinalExamPurchaseController::class, 'destroy'])->name('destroy');
            });
        });
    // All purchase routes


    // All policies related routes
        Route::resource('policy-categories', PolicyCategoryController::class)->except(['show']);
        Route::get('policy-categories/filter', [PolicyCategoryController::class, 'filter'])->name('policy-categories.filter');

        Route::resource('policies', PolicyController::class)->except(['show']);
        Route::get('policies/filter', [PolicyController::class, 'filter'])->name('policies.filter');
    // All policies related routes


    // Webinars routes
        Route::resource('webinars', WebinarController::class)->except('show');
        Route::get('webinars/filter', [WebinarController::class, 'filter'])->name('webinars.filter');
    // Webinars routes


    // Exam results routes
        Route::prefix('exam-results')->name('exam-results.')->group(function() {
            Route::get('module-exams', [ExamResultController::class, 'moduleExams'])->name('module-exams');
            Route::get('module-exams/filter', [ExamResultController::class, 'moduleExamsFilter'])->name('module-exam-filter');
            Route::get('module-exams/{course_module_exam}', [ExamResultController::class, 'moduleExamResult'])->name('module-exam-result');
            Route::delete('module-exams/{course_module_exam}', [ExamResultController::class, 'moduleExamResultDestroy'])->name('module-exam-result-destroy');
            
            Route::get('final-exams', [ExamResultController::class, 'finalExams'])->name('final-exams');
            Route::get('final-exams/filter', [ExamResultController::class, 'finalExamsFilter'])->name('final-exam-filter');
            Route::get('final-exams/{course_final_exam}', [ExamResultController::class, 'finalExamResult'])->name('final-exam-result');
            Route::delete('final-exams/{course_final_exam}', [ExamResultController::class, 'finalExamResultDestroy'])->name('final-exam-result-destroy');
        });
    // Exam results routes
});