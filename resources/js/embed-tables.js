import 'tablesort/tablesort.css';
import Tablesort from 'tablesort';

const loadNumberSort = (() => {
    let loaded = false;

    return async () => {
        if (loaded) {
            return;
        }

        if (typeof window !== 'undefined') {
            window.Tablesort = Tablesort;
        }

        await import('tablesort/src/sorts/tablesort.number');
        loaded = true;
    };
})();

const initSortableTables = async () => {
    await loadNumberSort();

    document.querySelectorAll('[data-sortable-table]').forEach((table) => {
        if (!table.dataset.tablesortBound) {
            new Tablesort(table);
            table.dataset.tablesortBound = 'true';
        }
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initSortableTables();
    });
} else {
    initSortableTables();
}
