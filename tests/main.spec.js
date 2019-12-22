describe("Nothing Private unit tests", function () {
    let fixture = null;
    beforeAll(function () {
        // To prevent call to API and fixing errors
        window.calculateFingerprint = () => {
        };
        window.saveFingerPrintAPICall = (...data) => {};
    });

    beforeEach(function () {
        fixture = document.createElement("div");
        fixture.id = "fixture";
        document.body.appendChild(fixture);
    });

    afterEach(function () {
        if (document.getElementById("fixture")) {
            document.body.removeChild(document.getElementById("fixture"));
        }
    });

    it("renderNewTabPage function should render the text properly", function () {
        const maindiv = document.createElement("div");
        maindiv.id = "maindiv";
        const user = document.createElement("div");
        user.id = "user";
        fixture.appendChild(maindiv);
        fixture.appendChild(user);
        const name = "Test";
        renderNewTabPage(name);
        expect(maindiv.innerHTML).toContain(name);
        expect(user.innerHTML).toContain(name);
    });

    it("htmlEncode function should encode values correctly", function () {
        const encoded = htmlEncode('<div>test<div>');
        expect(encoded).toBe('&lt;div&gt;test&lt;div&gt;');
    });

    it("renderSubmit function should render the text properly", function () {
        const maindiv = document.createElement("div");
        maindiv.id = "maindiv";
        fixture.appendChild(maindiv);
        const name = "Test";
        renderSubmit(name);
        expect(maindiv.innerHTML).toContain(name);
        expect(maindiv.innerHTML).toContain("Thank you");
    });

    it("renderMain function should render the text properly", function () {
        const maindiv = document.createElement("div");
        maindiv.id = "maindiv";
        fixture.appendChild(maindiv);
        renderMain();
        expect(maindiv.innerHTML).toContain("Do you think that switching");
    });

    it("reload function should render the text properly", function () {
        const maindiv = document.createElement("div");
        maindiv.id = "maindiv";
        fixture.appendChild(maindiv);
        reload();
        expect(maindiv.innerHTML).toContain("Loading");
    });

    it("errorHandler function should render the text properly", function () {
        const maindiv = document.createElement("div");
        maindiv.id = "maindiv";
        fixture.appendChild(maindiv);
        errorHandler();
        expect(maindiv.innerHTML).toContain("An API error occurred");
    });
});
