// scripts/check-vite-entries.mjs
import fs from "fs";
import path from "path";

const root = process.cwd();
const bladeDir = path.join(root, "resources", "views");
const manifestFile = path.join(root, "public", "build", "manifest.json");
const hotFile = path.join(root, "public", "hot");

function readBladeFiles(dir) {
    const files = fs.readdirSync(dir, { withFileTypes: true });
    let out = [];
    for (const f of files) {
        const p = path.join(dir, f.name);
        if (f.isDirectory()) out = out.concat(readBladeFiles(p));
        else if (f.isFile() && p.endsWith(".blade.php")) out.push(p);
    }
    return out;
}

function extractVitePaths(src) {
    // Match @vite('...') or @vite(["...", "..."]) possibly with whitespace/newlines
    const results = new Set();
    const viteCall = /@vite\s*\(\s*([\s\S]*?)\s*\)/g;
    let m;
    while ((m = viteCall.exec(src))) {
        const inside = m[1];
        const quoted = inside.match(/(['"])(.+?)\1/g) || [];
        for (const q of quoted) {
            const p = q.slice(1, -1); // remove quotes
            if (p.startsWith("resources/")) results.add(p);
        }
    }
    return results;
}

function fileExists(relPath) {
    return fs.existsSync(path.join(root, relPath));
}

const blades = readBladeFiles(bladeDir);
const referenced = new Set();
for (const f of blades) {
    const src = fs.readFileSync(f, "utf8");
    for (const p of extractVitePaths(src)) referenced.add(p);
}

const devActive = fs.existsSync(hotFile);
let manifestKeys = new Set();
if (!devActive && fs.existsSync(manifestFile)) {
    const manifest = JSON.parse(fs.readFileSync(manifestFile, "utf8"));
    for (const k of Object.keys(manifest)) manifestKeys.add(k);
}

const missingOnDisk = [...referenced].filter((p) => !fileExists(p));
const missingInManifest = devActive
    ? [] // dev server bypasses manifest
    : [...referenced].filter((p) => !manifestKeys.has(p));

const unreferencedInManifest = devActive
    ? []
    : [...manifestKeys].filter((k) => !referenced.has(k));

console.log(`\n=== Vite check ===`);
console.log(
    `Dev server active: ${
        devActive ? "YES (public/hot present)" : "NO (using manifest)"
    }`
);
console.log(`Blade @vite() entries:`);
for (const p of referenced) console.log("  -", p);

if (missingOnDisk.length) {
    console.log(`\n❌ Files referenced in Blade but NOT found on disk:`);
    for (const p of missingOnDisk) console.log("  -", p);
}

if (!devActive && missingInManifest.length) {
    console.log(
        `\n❌ Files referenced in Blade but NOT found in manifest (run build or fix vite inputs):`
    );
    for (const p of missingInManifest) console.log("  -", p);
}

if (!devActive && unreferencedInManifest.length) {
    console.log(
        `\n⚠️  Manifest entries not referenced by any Blade (safe to remove from vite inputs or leave):`
    );
    for (const k of unreferencedInManifest) console.log("  -", k);
}

if (!missingOnDisk.length && (!missingInManifest.length || devActive)) {
    console.log(`\n✅ Looks good.`);
}
console.log("");
