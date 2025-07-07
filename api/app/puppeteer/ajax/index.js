import puppeteer from "puppeteer";
import { userAgents } from "../shared/user-agents.js";
import { loadWithRetry } from "../shared/retry-load.js";

(async () => {
    const url = process.argv[2];
    const apiBaseUrl = process.argv[3];

    if (!url || !apiBaseUrl) {
        console.log("Invalid url or Api base url");
        process.exit(1);
    }

    const browser = await puppeteer.launch({
        defaultViewport: {
            width: 1920,
            height: 1080,
        },
    });

    let apiResponse = "";
    const error = { url };

    try {
        const page = await browser.newPage();

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

        page.on("request", async (request) => {
            const type = request.resourceType();
            const reqUrl = request.url();

            if (
                (type == "fetch" || type == "xhr") &&
                reqUrl.includes(apiBaseUrl)
            ) {
                apiResponse = reqUrl;
            }
        });

        const response = await loadWithRetry(page, url);

        if (!response) {
            error.status = 500;
            error.error = "No response received";
        } else {
            const status = response.status();

            if (status >= 400) {
                error.status = status;
                error.error = `${status} error`;
            }
        }
    } catch (err) {
        error.error = err.message;
        error.status = 500;
    } finally {
        await browser.close();
    }

    if (apiResponse) {
        console.log(apiResponse);
    } else {
        error.status = 500;
        error.error = "Invalid api base url";
        console.log(JSON.stringify(error));
        process.exit(1);
    }
})();
