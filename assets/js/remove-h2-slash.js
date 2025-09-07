/**
 * Remove leftover slash text nodes or wrappers from H2 headings.
 * Runs after DOM is ready to clean dynamically injected content.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('h2').forEach(function (h2) {
        var walker = document.createTreeWalker(
            h2,
            NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT,
            null
        );
        var toRemove = [];
        while (walker.nextNode()) {
            var node = walker.currentNode;
            if (node.nodeType === Node.TEXT_NODE) {
                var text = node.nodeValue.trim();
                if (text === '/' || text === '∕') {
                    toRemove.push(node);
                }
            } else {
                var content = node.textContent.trim();
                if (content === '/' || content === '∕') {
                    toRemove.push(node);
                }
            }
        }
        toRemove.forEach(function (node) {
            if (node.parentNode) {
                node.parentNode.removeChild(node);
            }
        });
    });
});
