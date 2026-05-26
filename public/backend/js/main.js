// html editor
document.querySelectorAll(".editor").forEach((element) => {
    ClassicEditor.create(element, {
        ckfinder: {
            uploadUrl: uploadUrl,
        },
        heading: {
            options: [
                {
                    model: "heading1",
                    view: "h1",
                    title: "Heading 1",
                    class: "ck-heading_heading1",
                },
                {
                    model: "heading2",
                    view: "h2",
                    title: "Heading 2",
                    class: "ck-heading_heading2",
                },
                {
                    model: "heading3",
                    view: "h3",
                    title: "Heading 3",
                    class: "ck-heading_heading3",
                },
                {
                    model: "paragraph",
                    title: "Paragraph",
                    class: "ck-heading_paragraph",
                },
            ],
        },
        mediaEmbed: {
            previewsInData: true,
            providers: [
                {
                    name: 'youtube',
                    url: [
                        /^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)/,
                        /^(?:m\.)?youtube\.com\/v\/([\w-]+)/,
                        /^youtube\.com\/embed\/([\w-]+)/,
                        /^youtu\.be\/([\w-]+)/,
                        /^youtube\.com\/shorts\/([\w-]+)/,
                    ],
                    html: match => {
                        const id = match[1];
                        return '<div style="position:relative;padding-bottom:56.2493%;height:0;">' +
                            `<iframe src="https://www.youtube.com/embed/${id}" ` +
                            'style="position:absolute;width:100%;height:100%;top:0;left:0;" ' +
                            'frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>' +
                            '</iframe></div>';
                    }
                },
                {
                    name: 'vimeo',
                    url: [
                        /^vimeo\.com\/([\d]+)/,
                        /^vimeo\.com\/[^/]+\/([\d]+)/,
                    ],
                    html: match => {
                        const id = match[1];
                        return '<div style="position:relative;padding-bottom:56.2493%;height:0;">' +
                            `<iframe src="https://player.vimeo.com/video/${id}" ` +
                            'style="position:absolute;width:100%;height:100%;top:0;left:0;" ' +
                            'frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>' +
                            '</iframe></div>';
                    }
                },
                {
                    name: 'dailymotion',
                    url: /^dailymotion\.com\/video\/([\w]+)/,
                    html: match => {
                        const id = match[1];
                        return '<div style="position:relative;padding-bottom:56.2493%;height:0;">' +
                            `<iframe src="https://www.dailymotion.com/embed/video/${id}" ` +
                            'style="position:absolute;width:100%;height:100%;top:0;left:0;" ' +
                            'frameborder="0" allowfullscreen>' +
                            '</iframe></div>';
                    }
                },
                {
                    name: 'tiktok',
                    url: /^(?:www\.)?tiktok\.com\/@[\w.-]+\/video\/([\d]+)/,
                    html: match => {
                        const id = match[1];
                        return '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">' +
                            `<iframe src="https://www.tiktok.com/embed/v2/${id}" ` +
                            'style="position:absolute;top:0;left:0;width:100%;height:100%;" ' +
                            'frameborder="0" allowfullscreen allow="encrypted-media">' +
                            '</iframe></div>';
                    }
                },
                {
                    name: 'facebook',
                    url: /^(?:www\.)?facebook\.com\/.+/,
                    html: match => {
                        const url = encodeURIComponent('https://' + match[0]);
                        return '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">' +
                            `<iframe src="https://www.facebook.com/plugins/video.php?href=${url}&show_text=false" ` +
                            'style="position:absolute;top:0;left:0;width:100%;height:100%;" ' +
                            'frameborder="0" allowfullscreen allow="autoplay; clipboard-write; encrypted-media; picture-in-picture">' +
                            '</iframe></div>';
                    }
                },
                {
                    name: 'allow-all',
                    url: /^.+$/,
                    html: match => {
                        const url = match[0];
                        return '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;">' +
                            `<iframe src="${url}" ` +
                            'style="position:absolute;top:0;left:0;width:100%;height:100%;" ' +
                            'frameborder="0" allowfullscreen>' +
                            '</iframe></div>';
                    }
                },
            ],
        },
    })
        .then((newEditor) => {})
        .catch((error) => {
            console.error(error);
        });
});
// html editor

// Prevent too many clicks
document.querySelectorAll("form").forEach(function (form) {
    form.addEventListener("submit", function (event) {
        if (form.checkValidity()) {
            form.querySelectorAll(".submit-button, .delete-button").forEach(
                function (button) {
                    button.disabled = true;
                },
            );
        } else {
            form.reportValidity();
            event.preventDefault();
        }
    });
});
// Prevent too many clicks

// Status update
function updateStatusToggle(routeTemplate, csrfToken, user) {
    $(".status-toggle").on("change", function () {
        let isChecked = $(this).prop("checked");
        let status = isChecked ? "1" : "2";
        let id = $(this).attr("id");
        let url = routeTemplate.replace(":user", user).replace(":id", id);

        let spinner = $(this).next(".spinner");
        spinner.show();

        $.ajax({
            url: url,
            type: "POST",
            data: {
                id: id,
                status: status,
                _token: csrfToken,
            },
            success: function (response) {
                spinner.hide();
                location.reload();
            },
            error: function (xhr) {
                console.log("An error occurred:", xhr.responseText);
            },
        });
    });
}
// Status update
