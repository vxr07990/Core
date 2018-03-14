package org.swissphpfriends.gradle.task

import org.gradle.api.*
import org.gradle.testfixtures.ProjectBuilder
import org.junit.Test

import static org.junit.Assert.*

class PhpUnitTest {

    @Test
    public void canAddTaskToProject() {
        Project project = ProjectBuilder.builder().build()
        def task = project.task('phpunit', type: PhpUnit)
        assertTrue(task instanceof PhpUnit)
    }

    @Test
    public void handlesParameterExecutable() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('executable'))
        assertEquals('phpunit',phpUnitTask.getProperty('executable'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(true,command1.contains('phpunit'));

        phpUnitTask.setProperty('executable', 'vendor/bin/phpunit');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('vendor/bin/phpunit'));
    }

    @Test
    public void handlesParameterVerbosity() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('verbose'))
        assertEquals(false,phpUnitTask.getProperty('verbose'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(false,command1.contains('-v'));

        phpUnitTask.setProperty('verbose', true);

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('-v'));
    }

    @Test
    public void handlesParameterPath() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('path'))
        assertEquals('./',phpUnitTask.getProperty('path'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(true,command1.contains('./'));

        phpUnitTask.setProperty('path', './src');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('./src'));
    }

    @Test
    public void handlesParameterOutputTap() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('outputTap'))
        assertEquals(false,phpUnitTask.getProperty('outputTap'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(false,command1.contains('--tap'));

        phpUnitTask.setProperty('outputTap', true);

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--tap'));
    }

    @Test
    public void handlesParameterOutputTestdox() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('outputTestdox'))
        assertEquals(false,phpUnitTask.getProperty('outputTestdox'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(false,command1.contains('--testdox'));

        phpUnitTask.setProperty('outputTestdox', true);

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--testdox'));
    }

    @Test
    public void handlesParameterGroups() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('groupIncludeMode'))
        assertEquals(false,phpUnitTask.getProperty('groupIncludeMode'))

        assertTrue(phpUnitTask.hasProperty('groupExcludeMode'))
        assertEquals(false,phpUnitTask.getProperty('groupExcludeMode'))

        // include
        phpUnitTask.setProperty('groupIncludeMode', true);
        phpUnitTask.setProperty('groups', 'unit');
        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(true,command1.contains('--group'));
        assertEquals(true,command1.contains('unit'));

        // exclude
        phpUnitTask.setProperty('groupIncludeMode', false);
        phpUnitTask.setProperty('groupExcludeMode', true);
        phpUnitTask.setProperty('groups', 'functional,integration');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--exclude-group'));
        assertEquals(true,command2.contains('functional,integration'));
    }

    @Test
    public void handlesParameterBootstrapFile() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('bootstrapFile'))
        assertEquals('',phpUnitTask.getProperty('bootstrapFile'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(false,command1.contains('--bootstrap'));

        phpUnitTask.setProperty('bootstrapFile', 'test/boostrap.php');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--bootstrap'));
        assertEquals(true,command2.contains('test/boostrap.php'));
    }

    @Test
    public void handlesParameterConfigurationFile() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('configurationFile'))
        assertEquals('',phpUnitTask.getProperty('configurationFile'))

        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(false,command1.contains('--configuration'));

        phpUnitTask.setProperty('configurationFile', 'test/phpunit.xml');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--configuration'));
        assertEquals(true,command2.contains('test/phpunit.xml'));
    }

    @Test
    public void handlesParameterCoverage() {
        Task phpUnitTask = this.getTaskInstance();

        assertTrue(phpUnitTask.hasProperty('coverageFormat'))
        assertEquals('none',phpUnitTask.getProperty('coverageFormat'))

        assertTrue(phpUnitTask.hasProperty('coverageTarget'))
        assertEquals('',phpUnitTask.getProperty('coverageTarget'))

        // text
        phpUnitTask.setProperty('coverageFormat', 'text');
        phpUnitTask.setProperty('coverageTarget', 'build/log/coverage.txt');
        ArrayList<String> command1 = phpUnitTask.buildCommand();
        assertEquals(true,command1.contains('--coverage-text'));
        assertEquals(true,command1.contains('build/log/coverage.txt'));

        // junit
        phpUnitTask.setProperty('coverageFormat', 'junit');
        phpUnitTask.setProperty('coverageTarget', 'build/log/junit.xml');

        ArrayList<String> command2 = phpUnitTask.buildCommand();
        assertEquals(true,command2.contains('--coverage-junit'));
        assertEquals(true,command2.contains('build/log/junit.xml'));
    }

    private Task getTaskInstance() {
        Project project = ProjectBuilder.builder().build()
        return project.task('phpunit', type: PhpUnit)
    }
}
