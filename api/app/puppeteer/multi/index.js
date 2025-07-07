import puppeteer from "puppeteer";
import { userAgents } from "../shared/user-agents.js";
import { loadWithRetry } from "../shared/retry-load.js";

(async () => {
    const urls = process.argv.slice(2);

    if (!urls.length) {
        console.log("No URL provided");
        process.exit(1);
    }

    const browser = await puppeteer.launch({
        defaultViewport: {
            width: 1920,
            height: 1080,
        },
    });

    const errors = [];

    const pages = await Promise.all(urls.map(() => browser.newPage()));

    const responses = await Promise.all(
        pages.map(async (page, i) => {
            try {
                const randomUserAgent =
                    userAgents[Math.floor(Math.random() * userAgents.length)];

                await page.setUserAgent(randomUserAgent);
                await page.setExtraHTTPHeaders({
                    "Accept-Language": "en-US,en;q=0.9",
                    "Sec-CH-UA":
                        '"Chromium";v="117", "Not)A;Brand";v="24", "Google Chrome";v="117"',
                    "Sec-CH-UA-Mobile": "?0",
                    "Sec-CH-UA-Platform": '"Windows"',
                });

                const response = await loadWithRetry(page, urls[i]);
                return { page, response, url: urls[i] };
            } catch (error) {
                errors.push({
                    url: urls[i],
                    error: error.message,
                    status: 500,
                });
                return null;
            }
        })
    );

    let results = await Promise.all(
        responses.map(async (response) => {
            if (!response || !response.response) return null;

            const status = response.response.status();

            if (status >= 400) {
                errors.push({
                    url: response.url,
                    error: `${status} error`,
                    status,
                });
                return null;
            }

            return await response.page.content();
        })
    );

    results = results.filter((result) => !!result);

    await browser.close();
    console.log(JSON.stringify({ results, errors }));
})();
