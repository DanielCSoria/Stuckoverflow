<?php
$search_value = "Search";
$nb_pages = Configuration::get("page_posts");
if (isset($filter) && $filter != "Search") {
    $search_value = Utils::url_safe_decode($filter);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stuck Overflow</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.css" rel="stylesheet"/>
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="css/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="view/utilsJs/loadUtils.js"></script>
    <script src="lib/url-tools-bundle.min.js"></script>
    <link href="css/stylebis.css" rel="stylesheet"/>

    <script>
        $(function() {
            var page = 1;
            var option = "newest";
            var filt = null;

            $("#search").next().hide();

            //when we click on tags, we unactive the current tab, update the "bonus tab"'s  (which is here for tags/search) text and get related posts.
            $(document).on('click', '.tags', function(e) {
                e.preventDefault();
                option = "tag";
                page = 1;
                $("#search").val("");
                unactiveTab();
                updateTaggedTab($(this).text());
                filt = $(this).data("value");
                getPosts(filt);
            });

            //simple pagination action
            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                page = parseInt($(this).attr('id')) + 1;
                getPosts(filt);
            });


            //similar to by tag, we need to update status of the "bonus tab" and retrieve post with given filter
            $("#search").on('keyup', function(e) {
                option = "search";
                page = 1;
                filt = $(this).val();
                unactiveTab();
                updateSearchTab(filt);
                filt = url_safe_encode(filt);
                getPosts(filt);
            });


            //"newest,votes,actives,unanswered", again very similar, as usual we need to update current search option and reset page to 1
            $(".tab-header").on('click', function(e) {
                e.preventDefault();
                unactiveTab();
                $("#search").val("");
                $(this).removeClass("unactive");
                $(this).addClass("active");
                option = $(this).attr("id");
                page = 1;
                $("#tagged").empty();
                getPosts();
            });


            //if filter is not null its tag or search and we pass another parameter called filter :p
            function getPosts(filter = null) {
                filter = filter == null ? "" : filter;
                let url = "post/get_posts_as_json/" + option + "/" + page + "/" + filter;
                $.get(url, function(data) {
                    displayPosts(JSON.parse(data.posts));
                    updatePagination(data.count);
                }, "json");

            }

            //usefull when changing tab
            function unactiveTab() {
                let currentActive = $("#tagged").parent().find('.active');
                currentActive.removeClass("active");
                currentActive.addClass("unactive");
            }

            //updating pagination to new result size 
            function updatePagination(nbMax) {
                let html = '';
                for (i = 0; i < nbMax/5; ++i)
                    html += '<li class="page-item ' + (i == (page - 1) ? 'active' : ' ') + '"><a href="#" id="' + i + '" class="page-link">' + (i + 1) + '</a></li>';
                $("#pagination").html(html);

            }

            function displayPosts(posts) {
                let html = "";
                let container = $("#postContainer");
                for (var i in posts) {
                    html += formatPost(posts[i]);
                }
                container.html(html);

            }

            $(document).on('click', '#tagged', function(e) {
                e.preventDefault();
            });

            //these are display fonctions, they mostly traduce server's answers to html
            function formatPost(post) {

                let html = '<div class="row row_size border-bottom active pt-3 pb-2 bg-light"><div class="col">';
                html += '<div class="row post_view"><div class="col text-center post_view_content">';
                html += '<h4 class="mb-0 ">' + post.voteCount + '</h4><p class="">Votes</p>';
                html += '</div><div class="col text-center post_view_content"><h4 class="mb-0">' + post.answCount + '</h4>';
                html += '<p>Answers</p></div></div></div><div class="col-md-9 link"><a class="title_link" href="post/show/' + post.id + '">' + post.title + '</a>';
                html += formatTags(post);
                html += "</div></div>";
                return html;
            }

            function formatTags(post) {
                let res = '<form class="form-inline">';
                res += '<p class="mr-2">' + post.time + '</p>';
                for (var i in post.tags) {
                    res += '<a  href="#" class="badge badge-primary rounded-0 mr-2 px-2 py-1 mb-3 tags" data-value="'+post.tags[i].id+'">' + post.tags[i].name + '</a>';
                }
                res += "</form>";
                return res;

            }

            function updateTaggedTab(tagName) {
                let html = '<a class="nav-link tab-header active" href="#">Questions tagged [' + tagName + ']</a>';
                $("#tagged").prev().removeClass("mr-5");
                $("#tagged").html(html);
            }

            function updateSearchTab(tagName) {
                let html = '<a class="nav-link tab-header active" href="#">Search [' + tagName + ']</a>';
                $("#tagged").prev().removeClass("mr-5");
                $("#tagged").html(html);
            }
        });
    </script>
</head>

<body class="bg p-5">
    <?php require("view/menu.php"); ?>
    <div class="card-header post_container inviss mx-auto opa">
        <ul class="nav nav-tabs card-header-tabs  mt-5">
            <li class="nav-item">
                <a id="newest" class="nav-link tab-header  <?= $active === "newest" ? 'active' : "unactive" ?>" href="post/index">Newest</a>
            </li>
            <li class="nav-item ml-1">
                <a id="vote" class="nav-link tab-header <?= $active === "vote" ? 'active' : "unactive" ?>" href="post/index/vote">Votes</a>
            </li>
            <li class="nav-item ml-1">
                <a id="actives" class="nav-link tab-header  <?= $active === "actives" ? 'active' : "unactive" ?>" href="post/index/actives">Actives</a>
            </li>
            <li class="nav-item ml-1 <?= $active === "tag" ? '' : "mr-5" ?>">
                <a id="unanswered" class="nav-link tab-header  <?= $active === "unanswered" ? 'active' : "unactive" ?>" href="post/index/unanswered">Unanswered</a>
            </li>

            <li id="tagged" class="nav-item ml-1">
                <?php if ($active == "tag") : ?>
                    <a class="nav-link tab-header active" href="post/index/tag/1/<?= $filter ?>">Questions tagged [<?= $tag_name ?>]</a>
                <?php endif; ?>
            </li>

            <li class="nav-item ml-5 mt-1">
                <form class="form-inline my-2 my-lg-0" method="post" action="post/launch_search/" id="searchForm">
                    <input class="form-control mr-sm-2 mini " id="search" name="search" type="search" placeholder="<?= $search_value ?>" aria-label="Search">
                    <button class="btn btn-primary my-2 my-sm-0 mini db" type="submit" id="buttonSubmit">Search</button>
                    <div class="result" id="result"></div>
                </form>
        </ul>
    </div>
    <div class="card-body post_container mx-auto mb-5 d-flex flex-column bg-light">
        <div id="postContainer">
            <?php
            if ($posts) :
                foreach ($posts as $post) :
            ?>

                    <div class="row row_size border-bottom active pt-3 pb-2 bg-light">
                        <div class="col">
                            <div class="row post_view">
                                <div class="col text-center post_view_content">
                                    <h4 class="mb-0 "><?= $post->get_vote_count() ?></h4>
                                    <p class="">Votes</p>
                                </div>
                                <div class="col text-center post_view_content">
                                    <h4 class="mb-0"><?= $post->get_answers_count() ?></h4>
                                    <p>Answers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 link">
                            <a class="title_link" href="post/show/<?= $post->get_id() ?>"><?= $post->get_title(); ?></a>
                            <form class="form-inline">
                                <p class="mr-2"><?= $post->get_time_info() ?></p>
                                <?php
                                foreach ($post->get_tags() as $tag) :
                                ?>
                                    <a href="post/index/tag/1/<?= $tag->get_id() ?>" data-value="<?=$tag->get_id()?>" class="badge badge-primary rounded-0 mr-2 px-2 py-1 mb-3 tags">
                                        <?= $tag->get_tag_name() ?>
                                    </a>
                                <?php
                                endforeach;
                                ?>
                            </form>
                        </div>
                    </div>

                <?php
                endforeach;
            else :
                ?>
                <div class="pt-4">
                    <h3>No posts found</h3>
                </div>
            <?php endif; ?>
        </div>
        <ul id="pagination" class="pagination  mx-auto mt-5">
            <?php for ($i = 0; $i < $nb_post / $nb_pages; ++$i) : ?>
                <li class="page-item <?= $i + 1 == $page ? 'active' : ' ' ?>"><a id="<?= $i ?>" href="post/index/<?= $active ?>/<?= $i + 1 ?>/<?= $active == "search" || $active == "tag" ? $filter : "" ?>" class="page-link"><?= $i + 1 ?></a></li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php require_once("view/loginModal.php"); ?>
    <?php require_once("view/logoutModal.php"); ?>
    <?php require_once("view/signupModal.php"); ?>


</body>

</html>